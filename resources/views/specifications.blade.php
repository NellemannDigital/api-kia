<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @vite(['resources/css/price-list.css'])
    </head>
    <body>
        <section class="mx-auto max-w-6xl space-y-8">
            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl">
                    {{ $car->name }} - Tekniske specifikationer
                </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>
        </section>

        <section class="mx-auto max-w-6xl space-y-6">
            @foreach($sections as $section)
                <div>
                     @if($section['show_header'])
                        <div class="flex items-center gap-2 p-2">
                            <div class="w-38"></div>
                            @foreach($section['columns'] as $group)
                                <div class="flex-1 text-center text-xs">
                                    {!! $group->label !!}
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="bg-primary p-2 rounded">
                        <span class="block font-bold text-xs text-white">{{ $section['title'] }}</span>
                    </div>

                    @foreach($section['rows'] as $row)
                        <div class="p-2 not-last:border-b border-primary-low">

                            <div class="flex items-center gap-2">
                                <div class="w-38">
                                    <span class="block text-xs">{!! $row['label'] !!}</span>
                                </div>

                                @foreach($section['columns'] as $col)
                                    @php $col = $group->columns->first(); @endphp
                                    
                                    <div class="flex-1">
                                        <span class="block text-center text-xs">{{ $row['value']($col) }}</span>
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