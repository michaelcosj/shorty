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
        $this->urls = auth()->user()->urls()->with('user')->latest()->get();

        $this->updateMetrics();
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
            $events["echo-private:metrics.{$shortUrl->id},ShortUrlMetricsUpdated"] = 'updateMetrics';
        }

        return $events;
    }

    public function updateMetrics(): void
    {
        $urlRequestMetrics = [];
        foreach ($this->urls as $shortUrl) {
            $requestCount = Redis::get('url:requests:' . $shortUrl->key);
            if (is_null($requestCount)) {
                $requestCount = 0;
            }

            $urlRequestMetrics[$shortUrl->id] = $requestCount;
        }

        $this->urlRequestMetrics = $urlRequestMetrics;
    }
}; ?>

<div class="mt-6 bg-white shadow-sm rounded-lg divide-y">
    @foreach ($urls as $url)
    <div class="p-6 flex space-x-2" wire:key="{{ $url->id }}">

        <div class="flex-1">
            <div class="flex justify-between items-center gap-12">
                <span class="text-gray-500 text-sm">{{ __('Redirects to: ') }}{{ $url->url }}</span>
                <div class="cursor-pointer" wire:click="delete({{ $url->id }})"
                    wire:confirm="Do you want to delete this item?">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>
            </div>
            <a href={{ route('go', ['key'=> $url->key]) }}>
                <p class="mt-4 text-lg text-gray-900">{{ route('go', ['key' => $url->key]) }}</p>
            </a>
            <div class="pt-3">
                <span class="text-gray-500 text-sm">{{ __('Total request count: ') }}{{ $urlRequestMetrics[$url->id]
                    }}</span>
            </div>
        </div>

    </div>
    @endforeach
</div>
<script>
    let data = null;
    document.addEventListener('livewire:load', () => {
        @this.on('print', () => {
            data = @this;
            console.log(data);
        })
    })
</script>
