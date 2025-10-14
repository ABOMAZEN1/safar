<?php
declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

final readonly class UserUpdateService
{
    public function updateProfile(array $data): User
    {
        return DB::transaction(function () use ($data) {
            /** @var User $user */
            $user = Auth::user();

            if (!$user) {
                throw new Exception(
                    'المستخدم غير موجود',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            // تحديث البيانات الأساسية
            $updateData = array_filter($data, function ($key) {
                return in_array($key, ['name', 'phone_number', 'firebase_token']);
            }, ARRAY_FILTER_USE_KEY);

            // تحديث Firebase token مع timestamp
            if (isset($updateData['firebase_token'])) {
                $updateData['firebase_token_updated_at'] = now();
            }

            $user->update($updateData);

            return $user->fresh();
        });
    }

    public function updateProfileImage(string $imagePath): User
    {
        return DB::transaction(function () use ($imagePath) {
            /** @var User $user */
            $user = Auth::user();

            if (!$user) {
                throw new Exception(
                    'المستخدم غير موجود',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->profile_image_path && Storage::exists($user->profile_image_path)) {
                Storage::delete($user->profile_image_path);
            }

            $user->update(['profile_image_path' => $imagePath]);

            return $user->fresh();
        });
    }

    public function updatePassword(string $currentPassword, string $newPassword): User
    {
        return DB::transaction(function () use ($currentPassword, $newPassword) {
            /** @var User $user */
            $user = Auth::user();

            if (!$user) {
                throw new Exception(
                    'المستخدم غير موجود',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            // التحقق من كلمة المرور الحالية
            if (!password_verify($currentPassword, $user->password)) {
                throw new Exception(
                    'كلمة المرور الحالية غير صحيحة',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $user->update(['password' => bcrypt($newPassword)]);

            return $user->fresh();
        });
    }

    public function getProfile(): User
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            throw new Exception(
                'المستخدم غير موجود',
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $user->load(['customer', 'company', 'driver', 'assistantDriver']);
    }
}