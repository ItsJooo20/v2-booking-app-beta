<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,technician,user,headmaster',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'phone' => $request->phone,
                'role' => $request->role ?? 'user',
                'is_active' => 1,
            ]);

            event(new Registered($user));

            return response()->json([
                'status' => true,
                'message' => 'Registration successful. Please check your email to verify your account.',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login details'
            ], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Email not verified',
                // 'data' => $user,
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'Bearer',
        //     'user' => $user,
        // ]);
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'access_token' => $token,
            ]
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email successfully verified']);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link resent.']);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
            'new_password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();
        
        $user = DB::table('users')->where('id', $userId)->first();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect'
            ], 401);
        }

        DB::table('users')
            ->where('id', $userId)
            ->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been updated successfully'
        ]);
    }

    public function changePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $existingUser = DB::table('users')
            ->where('phone', $request->new_phone)
            ->where('id', '!=', Auth::id())
            ->first();
            
        if ($existingUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'This phone number is already in use by another account'
            ], 422);
        }

        $userId = Auth::id();
        
        $user = DB::table('users')->where('id', $userId)->first();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect'
            ], 401);
        }

        DB::table('users')
            ->where('id', $userId)
            ->update(['phone' => $request->new_phone]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Phone number has been updated successfully'
        ]);
    }

    public function updateProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            
            // Get the image file
            $image = $request->file('profile_image');
            
            // Generate a unique filename
            $filename = 'profile_' . $userId . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            // Define the storage path
            $path = 'profile_images/' . $filename;
            
            // Delete old profile image if exists
            $user = Auth::user();
            if ($user && !empty($user->profile_image)) {
                // Extract filename from the full path
                $oldFilePath = Str::replaceFirst('storage/', '', $user->profile_image);
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }
            
            // Store the new image
            $image->storeAs('public/profile_images', $filename);
            
            // Update user profile with new image path
            $imagePath = 'storage/profile_images/' . $filename;
            
            // Update in database using query builder since some users might have issues with Eloquent
            DB::table('users')
                ->where('id', $userId)
                ->update(['profile_image' => $imagePath]);
            
            // Return success response with image URL
            return response()->json([
                'status' => 'success',
                'message' => 'Profile image updated successfully',
                'data' => [
                    'profile_image_url' => asset($imagePath)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeProfileImage()
    {
        try {
            $userId = Auth::id();
            $user = Auth::user();
            
            if ($user && !empty($user->profile_image)) {
                // Extract filename from the full path
                $filePath = Str::replaceFirst('storage/', '', $user->profile_image);
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                
                // Update user to remove profile image reference
                DB::table('users')
                    ->where('id', $userId)
                    ->update(['profile_image' => null]);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Profile image removed successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove image: ' . $e->getMessage()
            ], 500);
        }
    }
}