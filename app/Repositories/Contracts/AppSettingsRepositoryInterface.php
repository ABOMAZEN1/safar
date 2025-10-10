<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\AppSetting;

interface AppSettingsRepositoryInterface
{
    /**
     * Get app setting by ID.
     *
     * @param  int             $appSettingId The ID of the app setting
     * @return AppSetting|null The app setting if found, null otherwise
     */
    public function getAppSettingById(int $appSettingId): ?AppSetting;

    /**
     * Get app setting by key.
     *
     * @param  string          $key The key of the app setting
     * @return AppSetting|null The app setting if found, null otherwise
     */
    public function getAppSettingByKey(string $key): ?AppSetting;
}
