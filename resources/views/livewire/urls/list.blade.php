<?php

use App\Models\ShortUrl;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Redis;

new class extends Component {
    public Collection $urls;
    public array $urlRequestMetrics;

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
        $urlRequestMetrics = [];
        foreach ($this->urls as $shortUrl) {
            $requestCount = Redis::get('url:visits:' . $shortUrl->key);
            if (is_null($requestCount)) {
                $requestCount = 0;
            }

            $urlRequestMetrics[$shortUrl->id] = $requestCount;
        }

        $this->urlRequestMetrics = $urlRequestMetrics;
    }
}; ?>

<div class="mt-6 divide-y flex flex-col gap-2">
    @foreach ($urls as $url)
    <div class="shadow-sm rounded-lg bg-white p-6 flex space-x-2" wire:key="{{ $url->id }}">
        <div class="flex-1">
            <div class="flex justify-between items-center gap-12">
                <span class="text-gray-500 text-sm">{{ __('Redirects to: ') }}{{ $url->url }}</span>
                <div class="cursor-pointer" wire:click="delete({{ $url->id }})"
                    wire:confirm="Do you want to delete this item?">
                    <div class="h-8 w-8 flex justify-center items-center rounded-full hover:bg-red-600 group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 group-hover:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>
            <a href={{ route('go', ['key'=> $url->key]) }}>
                <p class="mt-4 text-lg text-gray-900">{{ route('go', ['key' => $url->key]) }}</p>
            </a>
            <div class="pt-3">
                <span class="text-gray-500 text-sm">{{ __('Visits: ') }}{{ $urlRequestMetrics[$url->id] }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>
