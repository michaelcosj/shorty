<?php

namespace App\Listeners;

use App\Events\ShortUrlAccessed;
use App\Events\ShortUrlMetricsUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Stevebauman\Location\Facades\Location;

class UpdateShortUrlMetric
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

        $metrics = Redis::command('INCR', ['url:requests:' . $shortUrlKey]);
        Log::info($metrics);

        // TODO
        // $location = Location::get($event->ip);
        // Log::info($location);
        //
        // // hincr 'url:location:' => $location.$key
        // if ($location) {
        //     Redis::command('HINCRBY', ['url:location', $location->cityName . $shortUrlKey, 1]);
        // }
        //
        ShortUrlMetricsUpdated::dispatch($event->shortUrl);
    }
}
