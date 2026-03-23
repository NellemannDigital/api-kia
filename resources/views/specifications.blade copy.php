<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @vite(['resources/css/price-list.css'])
    </head>
    <body>
        <section class="mx-auto max-w-6xl my-8 space-y-8">
            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl">
                    {{ $car->name }} - Tekniske specifikationer
                </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>
        </section>

        <section class="mx-auto max-w-6xl my-8 space-y-8">
            @foreach($sections as $section)
                <div>
                    <div class="flex gap-4 mb-2 bg-primary py-2 rounded text-white items-center">
                        <div class="font-bold text-xs w-32 flex items-end px-2">
                            {{ $section['title'] }}
                        </div>

                        @if($section['show_header'])
                            @foreach($section['columns'] as $col)
                                <div class="flex-1 text-xs">
                                    <span class="block font-bold">
                                        {{ $col->trim->name }}
                                    </span>
                                    <span class="block font-normal">
                                        {{ $col->powertrain->engine->name }}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    @foreach($section['rows'] as $row)
                        <div class="relative">

                            <div class="z-20 absolute inset-0 flex items-center border-primary-low @if(!$loop->last) border-b @endif h-full text-xs py-2">
                                <span class="w-32 mt-0.5">{{ $row['label'] }}</span>
                            </div>

                            <div class="flex gap-4">
                                <div class="w-32"></div>

                                @foreach($section['columns'] as $col)
                                    <div class="flex-1 text-xs p-2 text-center flex justify-center items-center border-primary-low border-x {{ $row['class'] }}">
                                        <span class="mt-0.5">{{ $row['value']($col) }} </span>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>

                @if($section['page_break'])
                    @pageBreak
                @endif
            @endforeach
        </section>

    </body>
</html>