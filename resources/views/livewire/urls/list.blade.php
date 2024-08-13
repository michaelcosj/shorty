<?php

use App\Models\ShortUrl;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Redis;

new class extends Component {
    public Collection $urls;
    public array $urlVisits;

    public function mount(): void
    {
        $this->getUrls();
    }

    #[On('url-shortened')]
    public function getUrls(): void
    {
        $this->urls = auth()->user()->urls()->latest()->get();
        $this->getVisits();
    }

    public function delete(ShortUrl $url): void
    {
        $this->authorize('delete', $url);
        $url->delete();
        $this->getUrls();
    }

    public function getListeners()
    {
        $events = [];
        foreach ($this->urls as $shortUrl) {
            $events["echo-private:visits.{$shortUrl->id},ShortUrlVisitsUpdated"] = 'getVisits';
        }

        return $events;
    }

    public function getVisits(): void
    {
        $urlVisits = [];
        foreach ($this->urls as $shortUrl) {
            $visits = Redis::get('url:visits:' . $shortUrl->key);
            if (is_null($visits)) {
                $visits = 0;
            }

            $urlVisits[$shortUrl->id] = $visits;
        }

        $this->urlVisits = $urlVisits;
    }
}; ?>

<div class="mt-6 divide-y flex flex-col gap-2">
    @foreach ($urls as $url)
    <div class="shadow-sm rounded-lg bg-white py-4 px-6 flex space-x-2" wire:key="{{ $url->id }}">
        <div class="flex-1 flex flex-col gap-4">
            <div class="flex flex-wrap gap-2 md:justify-between items-center w-full py-2">
                <div>
                    <x-secondary-button onclick="alert('TODO')" class="min-w-fit">
                        Download QR Code
                    </x-secondary-button>

                    <x-secondary-button onclick="addToClipboard('pussio');alert('Copied to clipboard')"
                        class="min-w-fit">
                        Copy
                    </x-secondary-button>
                </div>

                <x-danger-button wire:click="delete({{ $url->id }})" wire:confirm="Do you want to delete this item?">
                    Delete
                </x-danger-button>
            </div>
            <div class="flex flex-col">
                <a href="{{ route('go', ['key' => $url->key]) }}" class="text-blue-800 hover:underline hover:font-bold">
                    {{ route('go', ['key' => $url->key]) }}
                </a>
                <a href="{{ $url->url }}" class="text-black">{{ $url->url }}</a>
            </div>
            <div class="flex justify-end items-center gap-3">
                <p class="text-gray-800 text-sm">
                    {{ Carbon\Carbon::createFromTimeString($url->created_at)->toFormattedDayDateString() }}</p>
                <p class="text-gray-800 text-sm">{{ __('Visits: ') }}{{ $urlVisits[$url->id] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    function addToClipboard(text) {
        console.log("adding to clipboard...")
        navigator.clipboard.writeText(text).then(
            () => console.log(`successfully wrote ${text} to clipboard`),
            () => console.log(`failed writing ${text} to clipboard`)
        )
    }
</script>
