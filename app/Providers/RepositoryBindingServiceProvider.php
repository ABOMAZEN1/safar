<?php

declare(strict_types=1);

namespace App\Providers;

use Override;
use Illuminate\Support\ServiceProvider;

final class RepositoryBindingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[Override]
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
