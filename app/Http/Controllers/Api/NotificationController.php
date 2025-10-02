<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\FirebaseNotificationService;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Register FCM token dari aplikasi Android
     */
    public function registerToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'device_token' => 'required|string',
            'device_type' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = $request->user_id;
            $deviceToken = $request->device_token;

            // Hapus device token dari user lain (kalau ada)
            DeviceToken::where('device_token', $deviceToken)
                    ->where('user_id', '<>', $userId)
                    ->delete();

            // Simpan atau update token untuk user sekarang
            DeviceToken::updateOrCreate(
                [
                    'user_id' => $userId,
                    'device_token' => $deviceToken
                ],
                [
                    'device_type' => $request->device_type ?? 'android'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Token registered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim notifikasi ke SEMUA USER
     */
    public function sendToAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Ambil semua device tokens
        $tokens = DeviceToken::pluck('device_token')->toArray();

        if (empty($tokens)) {
            return response()->json([
                'success' => false,
                'message' => 'No device tokens found'
            ], 404);
        }

        $result = $this->firebaseService->sendToMultipleTokens(
            $tokens,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result);
    }

    /**
     * Kirim notifikasi ke USER TERTENTU
     */
    public function sendToUser(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah user ada
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Ambil device tokens user tertentu
        $tokens = DeviceToken::where('user_id', $userId)
                             ->pluck('device_token')
                             ->toArray();

        if (empty($tokens)) {
            return response()->json([
                'success' => false,
                'message' => 'No device tokens found for this user'
            ], 404);
        }

        $result = $this->firebaseService->sendToMultipleTokens(
            $tokens,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result);
    }
}