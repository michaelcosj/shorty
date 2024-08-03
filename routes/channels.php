<?php

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('visits.{shortUrl}', function (User $user, ShortUrl $shortUrl) {
    Log::info($shortUrl);
    return $user->id == $shortUrl->user_id;
});
