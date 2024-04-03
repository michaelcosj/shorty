<?php

use Hidehalo\Nanoid\Client;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    //
    #[Validate('required|string|max:255|url')]
    public string $url = '';

    public function store(): void {
        $this->validate();

        $nanoIDClient = new Client();
        auth()->user()->urls()->create([
         'key' => $nanoIDClient->generateId(7),
         'url' => $this->url,
        ]);

        $this->url = '';

        $this->dispatch('url-shortened');
    }
}; ?>

<div>
    <form wire:submit="store">

        <div class="flex gap-3 items-center w-full justify-center">
            <x-text-input
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                wire:model="url" placeholder="{{ __('Url to shorten') }}" />
            <x-primary-button>{{ __('Create') }}</x-primary-button>
        </div>
        <x-input-error :messages="$errors->get('url')" class="mt-2" />

    </form>
</div>
