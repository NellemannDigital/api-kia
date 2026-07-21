<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Specifikationer - ' . $car->name }}</title>

    @vite('resources/css/pdf.css')
</head>
<body>

    <main class="max-w-6xl mx-auto">

        <header class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold">
                {{ $car->name }} - Tekniske specifikationer
            </h1>

            <img
                src="{{ asset('images/logo.png') }}"
                alt="Logo"
                class="block w-20"
            >
        </header>

        @foreach($specifications['sections'] as $section)

            @if(
                !($section['b2b_only'] ?? false)
                || ($section['b2b_only'] && $car->variant->b2b)
            )

                @if($section['page_break'])
                    @pageBreak
                @endif

                <section class="space-y-2 mb-5">

                    @if($section['show_header'])
                        <div class="flex">
                            <div class="w-36 shrink-0"></div>

                            @foreach($specifications['columns'] as $group)
                                <div class="flex-1 text-center text-xs">
                                    @php
                                        $trimNames = collect($group['columns'])
                                            ->pluck('trim.name')
                                            ->join(', ', ' & ');

                                        $engineName = $group['columns'][0]['powertrain']->engine->name ?? '';
                                    @endphp

                                    <div class="font-bold">
                                        {{ $trimNames }}
                                    </div>

                                    <div class="font-normal">
                                        {{ $engineName }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="bg-primary rounded p-2 text-xs font-bold text-white">
                        {{ $section['title'] }}
                    </div>

                    <div class="divide-y divide-primary-low">

                        @foreach($section['rows'] as $row)

                            <div class="flex">

                                <div class="w-36 shrink-0 py-2 text-xs">
                                    {!! $row['labels'][$group['display_index']] ?? $row['label'] !!}
                                </div>

                                @foreach($specifications['columns'] as $group)
                                    <div class="flex flex-1 items-center justify-center border-l border-primary-low p-2 text-center text-xs">
                                        {{ $row['values'][$group['display_index']] ?? '-' }}
                                    </div>
                                @endforeach

                            </div>

                        @endforeach

                    </div>

                </section>

            @endif

        @endforeach

    </main>

</body>
</html>