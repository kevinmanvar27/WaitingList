<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle Google OAuth callback and create/login user
     */
    public function googleAuth(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'access_token' => 'required|string',
            ]);

            // Handle mock authentication for development
            if (str_starts_with($request->access_token, 'mock_access_token_')) {
                return $this->handleMockAuth();
            }

            // Get user info from Google using the access token
            $googleUser = Socialite::driver('google')->userFromToken($request->access_token);

            // Find or create user
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'google_id' => $googleUser->id,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'profile_picture' => $googleUser->avatar,
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update Google ID if user exists but doesn't have it
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'profile_picture' => $googleUser->avatar,
                    ]);
                }
            }

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_picture' => $user->profile_picture,
                        'has_pin' => !is_null($user->pin),
                        'is_admin' => $user->is_admin,
                    ],
                    'token' => $token,
                ],
                'message' => 'Authentication successful',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Get authenticated user details
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'created_at' => $user->created_at,
                'has_pin' => !empty($user->pin),
                'is_admin' => $user->is_admin,
            ],
            'message' => 'User details retrieved successfully',
        ]);
    }

    /**
     * Logout user and revoke tokens
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle mock authentication for development/testing
     */
    private function handleMockAuth(): JsonResponse
    {
        // Create or get a test user
        $user = User::where('email', 'test@example.com')->first();

        if (!$user) {
            $user = User::create([
                'google_id' => 'mock_google_id_123',
                'name' => 'Test User',
                'email' => 'test@example.com',
                'profile_picture' => 'https://via.placeholder.com/150',
                'email_verified_at' => now(),
            ]);
        }

        // Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_picture' => $user->profile_picture,
                    'has_pin' => !empty($user->pin),
                    'is_admin' => $user->is_admin,
                ],
                'token' => $token,
            ],
            'message' => 'Mock authentication successful',
        ]);
    }

    /**
     * Admin login with email and password
     */
    public function adminLogin(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password) || !$user->is_admin) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect or you are not an admin.'],
                ]);
            }

            // Create Sanctum token
            $token = $user->createToken('admin_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_picture' => $user->profile_picture,
                        'has_pin' => !empty($user->pin),
                        'is_admin' => $user->is_admin,
                    ],
                    'token' => $token,
                ],
                'message' => 'Admin authentication successful',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Set PIN for user after first Google OAuth login
     */
    public function setPin(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'pin' => 'required|string|min:4|max:6|regex:/^[0-9]{4,6}$/',
            ]);

            $user = $request->user();

            $user->update([
                'pin' => Hash::make($request->pin),
                'pin_set_at' => now(),
            ]);

            // Refresh user data to get updated fields
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'PIN set successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_picture' => $user->profile_picture,
                        'created_at' => $user->created_at,
                        'has_pin' => !empty($user->pin),
                        'is_admin' => $user->is_admin,
                    ]
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Set PIN error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to set PIN: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login with PIN
     */
    public function pinLogin(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'pin' => 'required|string|min:4|max:6|regex:/^[0-9]{4,6}$/',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->pin || !Hash::check($request->pin, $user->pin)) {
                throw ValidationException::withMessages([
                    'pin' => ['The provided PIN is incorrect.'],
                ]);
            }

            // Create Sanctum token
            $token = $user->createToken('pin_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_picture' => $user->profile_picture,
                        'has_pin' => !empty($user->pin),
                        'is_admin' => $user->is_admin,
                    ],
                    'token' => $token,
                ],
                'message' => 'PIN authentication successful',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('PIN login error: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'profile_picture' => 'nullable|url',
            ]);

            $user = $request->user();

            $user->update([
                'name' => $request->name,
                'profile_picture' => $request->profile_picture,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_picture' => $user->profile_picture,
                        'has_pin' => !is_null($user->pin),
                        'is_admin' => $user->is_admin,
                    ],
                ],
                'message' => 'Profile updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change user PIN
     */
    public function changePin(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_pin' => 'required|string|min:4|max:6|regex:/^[0-9]{4,6}$/',
                'new_pin' => 'required|string|min:4|max:6|regex:/^[0-9]{4,6}$/|different:current_pin',
                'confirm_pin' => 'required|string|min:4|max:6|regex:/^[0-9]{4,6}$/|same:new_pin',
            ]);

            $user = $request->user();

            // Check if user has a PIN set
            if (!$user->pin) {
                return response()->json([
                    'success' => false,
                    'message' => 'No PIN is currently set for this account. Please set a PIN first.',
                ], 422);
            }

            // Verify current PIN
            if (!Hash::check($request->current_pin, $user->pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current PIN is incorrect.',
                ], 422);
            }

            // Update to new PIN
            $user->update([
                'pin' => Hash::make($request->new_pin),
                'pin_set_at' => now(),
            ]);

            // Refresh user data to get updated fields
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'PIN changed successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_picture' => $user->profile_picture,
                        'created_at' => $user->created_at,
                        'has_pin' => !empty($user->pin),
                        'is_admin' => $user->is_admin,
                    ]
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Change PIN error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to change PIN: ' . $e->getMessage(),
            ], 500);
        }
    }
}
