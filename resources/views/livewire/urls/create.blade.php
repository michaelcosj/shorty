<?php

use Hidehalo\Nanoid\Client;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    //
    #[Validate('required|string|max:255|url')]
    public string $url = '';

    #[Validate('required|string|max:10|unique:short_urls,key')]
    public string $key = '';

    public function mount(): void
    {
        $this->generateKey();
    }

    public function store(): void
    {
        $this->validate();

        auth()
            ->user()
            ->urls()
            ->create([
                'key' => $this->key,
                'url' => $this->url,
            ]);

        $this->url = '';
        $this->generateKey();

        $this->dispatch('url-shortened');
    }

    public function generateKey(): void
    {
        $nanoIDClient = new Client();
        $this->key = $nanoIDClient->generateId(7);
    }
}; ?>

<div>
    <form wire:submit="store">

        <div class="flex flex-col md:flex-row gap-3 items-center w-full justify-center">
            <x-text-input
                class="min-w-fit block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                wire:model="url" placeholder="{{ __('www.example.com/something') }}" />
            <x-text-input
                class="min-w-fit block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                wire:model="key" placeholder="{{ __('Key') }}" />
            <x-primary-button>{{ __('Create') }}</x-primary-button>
        </div>
        <x-input-error :messages="$errors->get('url')" class="mt-2" />
        <x-input-error :messages="$errors->get('key')" class="mt-2" />

    </form>
</div>
