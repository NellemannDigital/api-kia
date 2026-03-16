<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        @vite(['resources/css/price-list.css'])
    </head>
    <body>
        <!-- Frontpage -->
        <section class="mx-auto w-[210mm] h-[305mm]">
            <div class="relative w-full h-full">
                @if($car->price_list->primary_image != null)
                    <img src="{{ $car->price_list->primary_image->url }}" class="bg-primary rounded-lg w-full h-full object-cover">
                @else
                    <div class="bg-primary rounded-lg w-full h-full object-cover"></div>
                @endif
                <img src="{{ asset('images/logo-white.png') }}" width="125" class="right-6 bottom-4 absolute">
                <div class="top-9 left-21 absolute font-bold text-white text-6xl">
                    {{ $car->name ?? '' }} 
                    <div class="mt-2 text-3xl">Tilbehør</div>
                </div>
                @if($car->campaign != null)
                    <img src="{{ $car->campaign->image->url }}?width=175" width="175" height="175" class="bottom-8 left-8 absolute">
                @endif
                <img src="{{ asset('images/motif.svg') }}" class="top-0 left-0 absolute w-full">
            </div>
        </section>

        @pageBreak


       @foreach($groupedAccessories as $category => $accessories)
            <section class="mx-auto max-w-4xl">
                <div class="flex justify-between items-center mb-3">
                    <div class="font-bold text-xl">{{ $category }}</div>
                    <img src="{{ asset('images/logo.png') }}" class="block w-20">
                </div>

                @foreach($accessories->chunk(9) as $chunk)
                    <div class="gap-2 grid grid-cols-3 gap-4">
                        @foreach ($chunk as $accessory)
                            <div class="flex flex-col rounded-lg border border-primary-low overflow-hidden h-88">

                                <img src="{{ $accessory->primary_image->url }}" 
                                    alt="{{ $accessory->name }}" 
                                    class="w-full h-48 object-cover" />

                                <div class="p-4 flex flex-col flex-1">

                                    <div class="font-bold text-sm line-clamp-2">
                                        {{ $accessory->name }}
                                    </div>

                                    <div class="mt-2 text-[12px] font-bold">
                                        {{ $accessory->prices->last()->price 
                                            ? Number::format($accessory->prices->last()->price, locale: 'da').' kr.' 
                                            : '-' }}
                                        <span class="text-[10px] font-normal">(inkl. moms og evt. montering)</span>
                                    </div>

                                    <div class="mt-auto flex flex-wrap gap-1 pt-2">
                                        @foreach($accessory->trim_names as $trim_name)
                                            <span class="inline-flex items-center rounded-full bg-primary-lowest px-2 py-0.5 text-[9px] text-primary">
                                                <span class="mt-px">{{ $trim_name }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(!$loop->last)
                        @pageBreak
                    @endif
                    
                @endforeach
                
            </section>
            @pageBreak
        @endforeach

    </body>
</html>