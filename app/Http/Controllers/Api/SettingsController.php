<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Get application settings
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $settings = Settings::getInstance();

            return response()->json([
                'success' => true,
                'data' => $settings,
                'message' => 'Settings retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update application settings
     */
    public function update(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'application_name' => 'sometimes|string|max:255',
                'app_version' => 'sometimes|string|max:50',
                'logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'favicon' => 'sometimes|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
                'app_logo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $settings = Settings::getInstance();
            $updateData = [];

            // Handle text fields
            if ($request->has('application_name')) {
                $updateData['application_name'] = $request->application_name;
            }

            if ($request->has('app_version')) {
                $updateData['app_version'] = $request->app_version;
            }

            // Handle file uploads
            foreach (['logo', 'favicon', 'app_logo'] as $field) {
                if ($request->hasFile($field)) {
                    // Delete old file if exists
                    if ($settings->$field && Storage::disk('public')->exists($settings->$field)) {
                        Storage::disk('public')->delete($settings->$field);
                    }

                    // Store new file
                    $path = $request->file($field)->store('settings', 'public');
                    $updateData[$field] = $path;
                }
            }

            $settings->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $settings->fresh(),
                'message' => 'Settings updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get public settings (for frontend)
     */
    public function public(): JsonResponse
    {
        try {
            $settings = Settings::getInstance();

            // Only return public settings
            $publicSettings = [
                'application_name' => $settings->application_name,
                'app_version' => $settings->app_version,
                'logo' => $settings->logo ? Storage::url($settings->logo) : null,
                'favicon' => $settings->favicon ? Storage::url($settings->favicon) : null,
                'app_logo' => $settings->app_logo ? Storage::url($settings->app_logo) : null,
            ];

            return response()->json([
                'success' => true,
                'data' => $publicSettings,
                'message' => 'Public settings retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve public settings: ' . $e->getMessage(),
            ], 500);
        }
    }
}
