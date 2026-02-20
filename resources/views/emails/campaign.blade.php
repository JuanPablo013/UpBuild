<x-mail::message>
    # Hola {{ $client->name }},

    {!! nl2br(e($content)) !!}

    @if(isset($customData['cta_text']) && isset($customData['cta_url']))
        <x-mail::button :url="$customData['cta_url']">
            {{ $customData['cta_text'] }}
        </x-mail::button>
    @endif

    <x-mail::panel>
        Esta es una campaña de **{{ $campaign->name }}**
    </x-mail::panel>

    Gracias,<br>
    {{ config('app.name') }}

</x-mail::message>