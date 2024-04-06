<?php

namespace App\Events;

use App\Models\ShortUrl;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShortUrlAccessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public ShortUrl $shortUrl,
        public string $ip
    ) {
    }
}
