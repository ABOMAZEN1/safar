<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\AppSetting;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class AppSettingsRepository
{
    /**
     * Get an application setting by its ID.
     *
     * @param  int             $appSettingId The unique identifier of the app setting
     * @return AppSetting|null Returns the AppSetting model if found, null otherwise
     */
    public function getAppSettingById(int $appSettingId): ?AppSetting
    {
        return AppSetting::findOrFail($appSettingId);
    }

    /**
     * Get app setting by key.
     *
     * @param  string          $key The key of the app setting
     * @return AppSetting      The app setting if found, throws ModelNotFoundException otherwise
     * @throws ModelNotFoundException When the setting with the given key is not found
     */
    public function getAppSettingByKey(string $key): AppSetting
    {
        return AppSetting::where('key', $key)->firstOrFail();
    }
}
