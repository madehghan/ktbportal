<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $loginLogs = $user->loginLogs()->orderBy('login_at', 'desc')->paginate(10);
        
        return view('profile.edit', [
            'user' => $user,
            'loginLogs' => $loginLogs,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'avatar' => 'required|string', // base64 encoded image
            ]);

            $user = $request->user();
            
            // Decode base64 image
            $imageData = $request->input('avatar');
            
            // Remove data:image/png;base64, or similar prefix
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageType = $matches[1];
            } else {
                return response()->json(['error' => 'فرمت تصویر نامعتبر است'], 422);
            }
            
            $imageData = base64_decode($imageData);
            
            if ($imageData === false) {
                return response()->json(['error' => 'خطا در پردازش تصویر'], 422);
            }
            
            // Ensure avatars directory exists
            $avatarsPath = storage_path('app/public/avatars');
            if (!file_exists($avatarsPath)) {
                if (!mkdir($avatarsPath, 0755, true)) {
                    \Log::error('Failed to create avatars directory', ['path' => $avatarsPath]);
                    return response()->json(['error' => 'خطا در ایجاد پوشه آپلود. لطفاً با مدیر سیستم تماس بگیرید.'], 500);
                }
            }
            
            // Check if directory is writable
            if (!is_writable($avatarsPath)) {
                \Log::error('Avatars directory is not writable', ['path' => $avatarsPath, 'permissions' => substr(sprintf('%o', fileperms($avatarsPath)), -4)]);
                return response()->json(['error' => 'پوشه آپلود قابل نوشتن نیست. لطفاً مجوزهای پوشه storage را بررسی کنید.'], 500);
            }
            
            // Generate unique filename
            $filename = 'avatars/' . $user->id . '_' . time() . '.' . $imageType;
            
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Save new avatar
            $saved = Storage::disk('public')->put($filename, $imageData);
            
            if (!$saved) {
                \Log::error('Failed to save avatar file', ['filename' => $filename, 'user_id' => $user->id]);
                return response()->json(['error' => 'خطا در ذخیره تصویر. لطفاً مجوزهای پوشه storage را بررسی کنید.'], 500);
            }
            
            // Verify file was actually saved
            if (!Storage::disk('public')->exists($filename)) {
                \Log::error('Avatar file was not saved', ['filename' => $filename, 'user_id' => $user->id]);
                return response()->json(['error' => 'تصویر ذخیره نشد. لطفاً مجوزهای پوشه storage را بررسی کنید.'], 500);
            }
            
            // Update user avatar
            $user->avatar = $filename;
            $user->save();
            
            return response()->json([
                'success' => true,
                'avatar_url' => asset('storage/' . $filename),
                'message' => 'تصویر پروفایل با موفقیت به‌روزرسانی شد'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating avatar', [
                'user_id' => $request->user()->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'خطا در آپلود تصویر: ' . $e->getMessage()
            ], 500);
        }
    }
}
