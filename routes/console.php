<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// ⚠️ Important: Schedule the telescope:prune command daily to prevent excessive accumulation of records in the telescope_entries table.

// Schedule::command('telescope:prune --hours=48')
//     ->daily()
//     ->sendOutputTo(storage_path('logs/telescope-prune.log'));
