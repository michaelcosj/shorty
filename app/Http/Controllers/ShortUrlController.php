<?php

namespace App\Http\Controllers;

use App\Events\ShortUrlAccessed;
use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    //
    public function index(): View
    {
        return view('urls', []);
    }

    public function go(Request $request, string $key): RedirectResponse
    {
        $shortUrl = ShortUrl::where('key', $key)->first();
        if (is_null($shortUrl)) {
            abort(404, 'route not found');
        }

        ShortUrlAccessed::dispatch($shortUrl, $request->ip());

        return redirect($shortUrl->url);
    }
}
