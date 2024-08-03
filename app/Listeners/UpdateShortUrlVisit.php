<?php

namespace App\Listeners;

use App\Events\ShortUrlAccessed;
use App\Events\ShortUrlVisitsUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UpdateShortUrlVisit
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
        $shortUrlKey = $event->shortUrl->key;

        $visits = Redis::command('INCR', ['url:visits:' . $shortUrlKey]);
        Log::info($visits);

        // TODO
        // Get location
        ShortUrlVisitsUpdated::dispatch($event->shortUrl);
    }
}
