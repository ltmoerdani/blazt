<?php

namespace App\Domain\User\Services;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Subscription;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use \Stripe\Stripe;
use \Stripe\Checkout\Session;
use \Stripe\Customer;
use \Stripe\PaymentMethod;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createSubscription(User $user, array $data): Subscription
    {
        return DB::transaction(function () use ($user, $data) {
            $subscription = $user->subscriptions()->create([
                'plan_name' => $data['plan_name'],
                'price' => $data['price'],
                'currency' => $data['currency'] ?? 'USD',
                'status' => $data['status'] ?? 'active',
                'starts_at' => $data['starts_at'] ?? now(),
                'ends_at' => $data['ends_at'] ?? now()->addMonth(),
                'payment_method' => $data['payment_method'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
            ]);

            // Update user's main subscription status
            $user->update([
                'subscription_plan' => $subscription->plan_name,
                'subscription_status' => $subscription->status,
                'subscription_expires_at' => $subscription->ends_at,
            ]);

            return $subscription;
        });
    }

    public function updateSubscription(Subscription $subscription, array $data): bool
    {
        return DB::transaction(function () use ($subscription, $data) {
            $updated = $subscription->update(array_filter([
                'plan_name' => $data['plan_name'] ?? null,
                'price' => $data['price'] ?? null,
                'currency' => $data['currency'] ?? null,
                'status' => $data['status'] ?? null,
                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
            ]));

            if ($updated) {
                // Ensure user's main subscription status is in sync
                $subscription->user->update([
                    'subscription_plan' => $subscription->plan_name,
                    'subscription_status' => $subscription->status,
                    'subscription_expires_at' => $subscription->ends_at,
                ]);
            }

            return $updated;
        });
    }

    public function cancelSubscription(Subscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            $cancelled = $subscription->update([
                'status' => 'cancelled',
                'ends_at' => now(), // End immediately on cancellation
            ]);

            if ($cancelled) {
                $subscription->user->update([
                    'subscription_status' => 'cancelled',
                    'subscription_expires_at' => now(),
                ]);
                // Optionally, cancel on Stripe if a Stripe subscription exists
                if ($subscription->stripe_subscription_id) {
                    try {
                        $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
                        $stripeSubscription->cancel();
                    } catch (Exception $e) {
                        // Log error but don't block the local cancellation
                        Log::error('Failed to cancel Stripe subscription: ' . $e->getMessage());
                    }
                }
            }

            return $cancelled;
        });
    }

    public function activateSubscription(Subscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            $activated = $subscription->update([
                'status' => 'active',
                'ends_at' => now()->addMonth(), // Extend for a month upon activation
            ]);

            if ($activated) {
                $subscription->user->update([
                    'subscription_status' => 'active',
                    'subscription_expires_at' => $subscription->ends_at,
                ]);
            }

            return $activated;
        });
    }

    public function createStripeCheckoutSession(User $user, string $planName, string $priceId, string $successUrl, string $cancelUrl): Session
    {
        try {
            // Create a Stripe Customer if not exists
            if (!$user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                $customer = Customer::retrieve($user->stripe_customer_id);
            }

            return Session::create([
                'customer' => $customer->id,
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_name' => $planName,
                ],
            ]);
        } catch (SubscriptionException $e) {
            throw new SubscriptionException('Stripe Checkout Session creation failed: ' . $e->getMessage());
        }
    }

    public function getActiveSubscription(User $user): ?Subscription
    {
        return $user->subscriptions()->where('status', 'active')->latest('ends_at')->first();
    }

    public function updatePaymentMethod(User $user, string $paymentMethodId): void
    {
        try {
            if (!$user->stripe_customer_id) {
                throw new SubscriptionException('Stripe customer ID not found for user.');
            }

            // Attach the new payment method to the customer
            PaymentMethod::retrieve($paymentMethodId)->attach([
                'customer' => $user->stripe_customer_id,
            ]);

            // Set the new payment method as default
            Customer::update(
                $user->stripe_customer_id,
                ['invoice_settings' => ['default_payment_method' => $paymentMethodId]]
            );

            // Optionally, update existing subscriptions to use the new payment method
            // This might depend on your Stripe setup and desired behavior
            $activeSubscription = $this->getActiveSubscription($user);
            if ($activeSubscription && $activeSubscription->stripe_subscription_id) {
                $stripeSubscription = \Stripe\Subscription::retrieve($activeSubscription->stripe_subscription_id);
                $stripeSubscription->default_payment_method = $paymentMethodId;
                $stripeSubscription->update(['default_payment_method' => $paymentMethodId]);
            }
        } catch (SubscriptionException $e) {
            throw new SubscriptionException('Failed to update payment method: ' . $e->getMessage());
        }
    }
}
