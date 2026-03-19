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
                    <div class="mt-2 text-4xl">Tilbehør</div>
                </div>
                @if($car->campaign != null)
                    <img src="{{ $car->campaign->image->url }}?width=175" width="175" height="175" class="bottom-8 left-8 absolute">
                @endif
                <img src="{{ asset('images/motif.svg') }}" class="top-0 left-0 absolute w-full">
            </div>
        </section>

        @pageBreak


       @foreach($pages as $page)

<section class="mx-auto max-w-4xl space-y-3">

    @php $currentCategory = null; @endphp

    @foreach($page as $row)

        @if($currentCategory !== $row['category'])

            <div class="flex justify-between items-center {{ $currentCategory === null ? '' : 'mt-6' }}">
                <div class="font-bold text-xl">{{ $row['category'] }}</div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>

            @php $currentCategory = $row['category']; @endphp

        @endif

        <div class="grid grid-cols-3 gap-4">

            @foreach ($row['items'] as $accessory)

                <div class="flex flex-col rounded-lg border border-primary-low overflow-hidden h-84">

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
                                    {{ $trim_name }}
                                </span>
                            @endforeach
                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @endforeach

</section>

@if(!$loop->last)
    @pageBreak
@endif

@endforeach

    </body>
</html>