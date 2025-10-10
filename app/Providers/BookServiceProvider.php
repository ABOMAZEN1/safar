<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Book\BookService;
use App\Services\Book\BookCreationService;
use App\Services\Book\BookCancellationService;
use App\Services\Book\BookQueryService;
use App\Services\Book\BookConfirmationService;
use App\Services\Book\BookValidationService;

class BookServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BookValidationService::class);

        $this->app->singleton(BookCreationService::class);
        $this->app->singleton(BookCancellationService::class);
        $this->app->singleton(BookQueryService::class);
        $this->app->singleton(BookConfirmationService::class);

        $this->app->singleton(BookService::class);
    }
}
