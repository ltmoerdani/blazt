<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\User\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    private const MESSAGE_UNAUTHENTICATED = 'Unauthenticated.';
    private const REQUIRED_STRING = 'required|string';

    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function createCheckoutSession(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => self::MESSAGE_UNAUTHENTICATED], 401);
        }

        $request->validate([
            'plan_name' => self::REQUIRED_STRING,
            'price_id' => self::REQUIRED_STRING, // Stripe Price ID
            'success_url' => 'required|url',
            'cancel_url' => 'required|url',
        ]);

        try {
            $session = $this->subscriptionService->createStripeCheckoutSession(
                $user,
                $request->plan_name,
                $request->price_id,
                $request->success_url,
                $request->cancel_url
            );

            return response()->json(['id' => $session->id, 'url' => $session->url], 200);
        } catch (Exception $e) {
            Log::error('Error creating Stripe checkout session: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create checkout session.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserSubscription()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => self::MESSAGE_UNAUTHENTICATED], 401);
        }

        $response = null;

        try {
            $subscription = $this->subscriptionService->getActiveSubscription($user);
            if (!$subscription) {
                $response = response()->json(['message' => 'No active subscription found.'], 404);
            } else {
                $response = response()->json($subscription, 200);
            }
        } catch (Exception $e) {
            Log::error('Error fetching user subscription: ' . $e->getMessage());
            $response = response()->json(['message' => 'Failed to fetch subscription.', 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function cancelSubscription()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => self::MESSAGE_UNAUTHENTICATED], 401);
        }

        $response = null;

        try {
            $subscription = $this->subscriptionService->getActiveSubscription($user);
            if (!$subscription) {
                $response = response()->json(['message' => 'No active subscription found to cancel.'], 404);
            } else {
                $this->subscriptionService->cancelSubscription($subscription);
                $response = response()->json(['message' => 'Subscription cancelled successfully.'], 200);
            }
        } catch (Exception $e) {
            Log::error('Error cancelling subscription: ' . $e->getMessage());
            $response = response()->json(['message' => 'Failed to cancel subscription.', 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function updatePaymentMethod(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => self::MESSAGE_UNAUTHENTICATED], 401);
        }

        $request->validate([
            'payment_method_id' => self::REQUIRED_STRING, // Stripe Payment Method ID
        ]);

        try {
            $this->subscriptionService->updatePaymentMethod($user, $request->payment_method_id);
            return response()->json(['message' => 'Payment method updated successfully.'], 200);
        } catch (Exception $e) {
            Log::error('Error updating payment method: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update payment method.', 'error' => $e->getMessage()], 500);
        }
    }
}
