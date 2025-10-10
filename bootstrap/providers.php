<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\YesPanelProvider::class,
    App\Providers\ObserverServiceProvider::class,
    App\Providers\RepositoryBindingServiceProvider::class,
    Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
    Clockwork\Support\Laravel\ClockworkServiceProvider::class,
    Laravel\Telescope\TelescopeServiceProvider::class,
];
