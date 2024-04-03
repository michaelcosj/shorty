<?php

namespace App\Listeners;

use App\Events\ShortUrlAccessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateShortUrlMetrics
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ShortUrlAccessed $event): void
    {
        //
    }
}
