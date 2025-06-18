<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Domain\User\Services\UserService;
use App\Domain\AI\Models\AIConfiguration;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Domain\User\Models\User;

class SettingsController extends Controller
{
    private const LOGIN_ROUTE = '/login';

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect(self::LOGIN_ROUTE);
        }

        $aiConfig = $user->aiConfigurations;
        $userLimits = $user->userLimit;

        return view('dashboard.settings.index', compact('user', 'aiConfig', 'userLimits'));
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect(self::LOGIN_ROUTE);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'timezone' => 'nullable|string|max:50',
        ]);

        try {
            $this->userService->updateUser($user, $request->only(['name', 'email', 'timezone']));
            return back()->with('success', 'Profile updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
    }

    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect(self::LOGIN_ROUTE);
        }

        $request->validate([
            'current_password' => ['required', 'string', function ($value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The provided password does not match your current password.');
                }
            }],
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $this->userService->updateUser($user, ['password' => $request->password]);
            return back()->with('success', 'Password updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update password: ' . $e->getMessage()]);
        }
    }

    public function updateAIConfiguration(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect(self::LOGIN_ROUTE);
        }

        $request->validate([
            'provider' => 'required|string|in:openai,deepseek,claude',
            'api_key' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            $aiConfig = $user->aiConfigurations()->firstOrNew(['user_id' => $user->id]);
            $aiConfig->fill([
                'provider' => $request->provider,
                'api_key' => $request->api_key,
                'is_active' => $request->is_active,
            ])->save();

            return back()->with('success', 'AI Configuration updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating AI configuration: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update AI configuration: ' . $e->getMessage()]);
        }
    }
}
