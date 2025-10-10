<?php

declare(strict_types=1);

/**
 * bootstrap/schedule.php.
 *
 * Registers scheduled commands for the application.
 */

use Illuminate\Console\Scheduling\Schedule;

/** @var Schedule $schedule */
$schedule->daily()
    ->onOneServer()
    ->withoutOverlapping()
    ->group(function () use ($schedule) {
        $schedule->command('telescope:prune --hours=48')->daily();

        $schedule->command('model:prune')->daily();

        $schedule->command('cache:prune-stale-tags')->daily();
    });

// Process scheduled notifications every minute
$schedule->command('notifications:process-scheduled')
    ->everyMinute()
    ->withoutOverlapping();
