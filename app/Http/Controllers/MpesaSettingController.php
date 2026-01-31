<?php

namespace App\Http\Controllers;

use App\Models\MpesaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MpesaSettingController extends Controller
{
    /**
     * Get the active M-Pesa settings
     */
    public function index()
    {
        $settings = MpesaSetting::getActive();

        if (!$settings) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'No M-Pesa configuration found'
            ]);
        }

        // Return settings without sensitive data for display
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $settings->id,
                'shortcode' => $settings->shortcode,
                'base_url' => $settings->base_url,
                'callback_url' => $settings->callback_url,
                'environment' => $settings->environment,
                'is_active' => $settings->is_active,
                'has_consumer_key' => !empty($settings->consumer_key),
                'has_consumer_secret' => !empty($settings->consumer_secret),
                'has_passkey' => !empty($settings->passkey),
                'created_at' => $settings->created_at,
                'updated_at' => $settings->updated_at,
            ]
        ]);
    }

    /**
     * Store or update M-Pesa settings
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consumer_key' => 'required|string|max:255',
            'consumer_secret' => 'required|string|max:255',
            'shortcode' => 'required|string|max:20',
            'passkey' => 'required|string',
            'base_url' => 'nullable|url|max:255',
            'callback_url' => 'nullable|url|max:255',
            'environment' => 'required|in:sandbox,production',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Deactivate all existing settings
            MpesaSetting::query()->update(['is_active' => false]);

            // Create or update settings
            $settings = MpesaSetting::create([
                'consumer_key' => $request->consumer_key,
                'consumer_secret' => $request->consumer_secret,
                'shortcode' => $request->shortcode,
                'passkey' => $request->passkey,
                'base_url' => $request->base_url ?? 'https://api.safaricom.co.ke',
                'callback_url' => $request->callback_url ?? url('/api/mpesa/callback'),
                'environment' => $request->environment,
                'is_active' => true,
            ]);

            Log::info('M-Pesa settings saved', [
                'id' => $settings->id,
                'shortcode' => $settings->shortcode,
                'environment' => $settings->environment,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'M-Pesa settings saved successfully',
                'data' => [
                    'id' => $settings->id,
                    'shortcode' => $settings->shortcode,
                    'environment' => $settings->environment,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving M-Pesa settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save M-Pesa settings'
            ], 500);
        }
    }

    /**
     * Update specific M-Pesa setting
     */
    public function update(Request $request, $id)
    {
        $settings = MpesaSetting::find($id);

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'M-Pesa settings not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'consumer_key' => 'nullable|string|max:255',
            'consumer_secret' => 'nullable|string|max:255',
            'shortcode' => 'nullable|string|max:20',
            'passkey' => 'nullable|string',
            'base_url' => 'nullable|url|max:255',
            'callback_url' => 'nullable|url|max:255',
            'environment' => 'nullable|in:sandbox,production',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $settings->update($request->only([
                'consumer_key',
                'consumer_secret',
                'shortcode',
                'passkey',
                'base_url',
                'callback_url',
                'environment',
            ]));

            Log::info('M-Pesa settings updated', [
                'id' => $settings->id,
                'shortcode' => $settings->shortcode,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'M-Pesa settings updated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating M-Pesa settings', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update M-Pesa settings'
            ], 500);
        }
    }

    /**
     * Delete M-Pesa settings
     */
    public function destroy($id)
    {
        $settings = MpesaSetting::find($id);

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'M-Pesa settings not found'
            ], 404);
        }

        try {
            $settings->delete();

            Log::info('M-Pesa settings deleted', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'M-Pesa settings deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting M-Pesa settings', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete M-Pesa settings'
            ], 500);
        }
    }

    /**
     * Test M-Pesa connection
     */
    public function testConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consumer_key' => 'required|string',
            'consumer_secret' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $credentials = base64_encode($request->consumer_key . ':' . $request->consumer_secret);
            $baseUrl = $request->base_url ?? 'https://api.safaricom.co.ke';

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
            ])->get($baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['access_token'])) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Connection successful! Credentials are valid.',
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Connection failed. Please check your credentials.',
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa connection test failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}