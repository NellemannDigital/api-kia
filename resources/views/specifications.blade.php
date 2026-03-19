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
            @foreach($sections as $section)
                <div>
                    <div class="flex gap-4 mb-2">
                        <div class="font-bold text-sm w-32 flex items-end">
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

                            <div class="z-20 absolute inset-0 flex items-center border-primary-low @if(!$loop->last) border-b @endif h-full text-xs">
                                {{ $row['label'] }}
                            </div>

                            <div class="flex gap-4">
                                <div class="w-32"></div>

                                @foreach($section['columns'] as $col)
                                    <div class="flex-1 text-xs text-center p-2 {{ $row['class'] }}">
                                        {{ $row['value']($col) }} 
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>
            @endforeach
        </section>

    </body>
</html>