<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Override;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Schema;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->register(BookServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        JsonResource::withoutWrapping();

        $this->configureModels();

        $this->configureCommands();

        RateLimiter::for('api', fn(Request $request): Limit => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        Password::defaults(
            callback: fn(): Password => Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
        );
    }

    /**
     * Configure the application's models.
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict();
        Model::unguard();
    }

    /**
     * Configure the application's commands.
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            $this->app->environment('production'),
        );
    }
}
