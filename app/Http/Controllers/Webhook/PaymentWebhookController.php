<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Domain\User\Services\SubscriptionService;
use App\Domain\User\Models\User;
use App\Domain\User\Models\Subscription;
use Exception;

class PaymentWebhookController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function handleStripeWebhook(Request $request)
    {
        Log::info('Incoming Stripe Webhook:', $request->all());

        // Stripe webhook secret for security
        $stripeWebhookSecret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $stripeWebhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Stripe Webhook Error: Invalid payload - ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Stripe Webhook Error: Invalid signature - ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $checkoutSession = $event->data->object;
                $this->handleCheckoutSessionCompleted($checkoutSession);
                break;
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $this->handleInvoicePaymentSucceeded($invoice);
                break;
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->handleSubscriptionDeleted($subscription);
                break;
            // ... handle other event types
            default:
                Log::info('Unhandled Stripe event type: ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function handleCheckoutSessionCompleted($checkoutSession)
    {
        $userId = $checkoutSession->metadata->user_id ?? null;
        $subscriptionId = $checkoutSession->metadata->subscription_id ?? null; // If existing subscription updated
        $planName = $checkoutSession->metadata->plan_name ?? 'default';
        $amountTotal = $checkoutSession->amount_total / 100; // Convert cents to dollars
        $currency = strtoupper($checkoutSession->currency);
        $transactionId = $checkoutSession->id;

        $user = User::find($userId);
        if (!$user) {
            Log::error('Stripe Webhook: User not found for checkout session ' . $transactionId);
            return;
        }

        try {
            if ($subscriptionId) {
                $subscription = Subscription::find($subscriptionId);
                if ($subscription && $subscription->user_id === $user->id) {
                    $this->subscriptionService->updateSubscription($subscription, [
                        'plan_name' => $planName,
                        'price' => $amountTotal,
                        'currency' => $currency,
                        'status' => 'active',
                        'transaction_id' => $transactionId,
                        'ends_at' => now()->addMonth(), // Assuming monthly subscription
                    ]);
                    Log::info('Stripe Webhook: Updated existing subscription for user ' . $user->id);
                }
            } else {
                // Create new subscription
                $this->subscriptionService->createSubscription($user, [
                    'plan_name' => $planName,
                    'price' => $amountTotal,
                    'currency' => $currency,
                    'status' => 'active',
                    'transaction_id' => $transactionId,
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                    'payment_method' => 'stripe',
                ]);
                Log::info('Stripe Webhook: Created new subscription for user ' . $user->id);
            }
        } catch (Exception $e) {
            Log::error('Stripe Webhook Error handling checkout.session.completed: ' . $e->getMessage());
        }
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        $subscriptionId = $invoice->subscription;
        $status = $invoice->status === 'paid' ? 'active' : 'failed'; // Or other statuses
        $transactionId = $invoice->id;

        $subscription = Subscription::where('transaction_id', $subscriptionId)->first();
        if (!$subscription) {
            Log::warning('Stripe Webhook: Subscription not found for invoice payment succeeded: ' . $subscriptionId);
            // Fallback to searching by original Stripe subscription ID if stored
            $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();
        }

        if ($subscription) {
            try {
                $this->subscriptionService->updateSubscription($subscription, [
                    'status' => $status,
                    'ends_at' => $subscription->ends_at->addMonth(), // Extend subscription
                    'transaction_id' => $transactionId, // Update with latest invoice ID
                ]);
                Log::info('Stripe Webhook: Invoice payment succeeded for subscription ' . $subscription->id);
            } catch (Exception $e) {
                Log::error('Stripe Webhook Error handling invoice.payment_succeeded: ' . $e->getMessage());
            }
        }
    }

    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            try {
                $this->subscriptionService->cancelSubscription($subscription);
                Log::info('Stripe Webhook: Subscription ' . $subscription->id . ' cancelled.');
            } catch (Exception $e) {
                Log::error('Stripe Webhook Error handling customer.subscription.deleted: ' . $e->getMessage());
            }
        } else {
            Log::warning('Stripe Webhook: Subscription not found for deletion: ' . $stripeSubscription->id);
        }
    }
}
