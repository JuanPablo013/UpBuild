<x-filament::page>
    <div class="fi-page-content space-y-6">

        {{ $this->form }}

        <x-filament::button
            wire:click="send"
            wire:loading.attr="disabled"
            color="primary"
            size="lg"
        >
            <span wire:loading.remove>Enviar</span>
            <span wire:loading>Procesando...</span>
        </x-filament::button>

        @if ($response)
            <div class="fi-card p-4 rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-500 mb-2">Respuesta de la IA:</p>
                <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $response }}</p>
            </div>
        @endif

    </div>
</x-filament::page>