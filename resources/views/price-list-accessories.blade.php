<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ 'Prisliste - ' . $car->name }}</title>

        @vite(['resources/css/price-list.css'])
    </head>
    <body>

        <!-- Frontpage -->
        <section class="mx-auto w-[210mm] h-[301mm]">
            <div class="relative w-full h-full">

                <img src="{{ $car->price_list?->primary_image?->url }}" class="w-full h-full object-cover rounded-lg">
                
                @if($car->urls?->test_drive)
                    <div class="right-6 bottom-6 absolute space-y-3">
                        <div class="bg-primary p-1.5 inline-block space-y-1.5 rounded-lg">
                            <div class="rounded-lg overflow-hidden">
                                <x-qr-code
                                    data="{{ $car->urls->test_drive }}"
                                    size="90"
                                />
                            </div>
                            <div class="text-white text-[9px] font-bold text-center">Book en prøvetur</div>
                        </div>
                    </div>
                @endif
               
                <div class="top-9 left-21 absolute font-bold text-white text-6xl">
                    {{ $car->name }}
                    <div class="mt-2 text-3xl">Tilbehør</div>
                </div>

                @if($car->campaign?->image?->url)
                    <img src="{{ $car->campaign?->image?->url }}?width=175" width="175" height="175"
                        class="bottom-8 left-8 absolute">
                @endif

                <img src="{{ asset('images/motif.svg') }}" class="top-0 left-0 absolute w-full">
            </div>
        </section>

        
        @foreach($accessories as $page)

        <section class="mx-auto max-w-[210mm] space-y-3">

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

                        <div class="flex flex-col rounded-lg overflow-hidden border border-primary-low h-80">

                            <img src="{{ $accessory->primary_image->url }}?width=300"
                                alt="{{ $accessory->name }}"
                                class="w-full h-40 object-cover" />

                            <div class="p-3 flex flex-col flex-1 border-t border-primary-low">

                                <div class="font-bold text-[12px] line-clamp-2 mb-2">
                                    {{ $accessory->name }}
                                </div>

                                
                                @if($car->variant->b2b && $accessory->prices->last()->price_ex_vat)

                                    <div class="text-[10px] mb-px">
                                        {{ $accessory->prices->last()->price_ex_vat
                                            ? Number::format($accessory->prices->last()->price_ex_vat, locale: 'da') . ' kr.'
                                            : '-' }}
                                        <span class="text-[10px] font-normal">(ekskl. moms)</span>
                                    </div>

                                @endif

                                <div class="text-[10px]">
                                    {{ $accessory->prices->last()->price
                                        ? Number::format($accessory->prices->last()->price, locale: 'da') . ' kr.'
                                        : '-' }}
                                    <span class="text-[10px] font-normal">(inkl. moms)</span>
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

            <div class="space-y-1 text-xs text-primary">
            
                @php
                    $priceListAccessoriesInstallation = compliance_text_for(['car' => $car], 'price_list_accessories_installation');
                    $priceListAccessoriesWarranty = compliance_text_for(['car' => $car], 'price_list_accessories_warranty');
                @endphp

                @if($priceListAccessoriesInstallation)
                    <div>
                        {{ $priceListAccessoriesInstallation }}
                    </div>
                @endif

                @if($priceListAccessoriesWarranty)
                    <div>
                        {{ $priceListAccessoriesWarranty }}
                    </div>
                @endif

            </div>


        </section>

        @if(!$loop->last)
            @pageBreak
        @endif

        @endforeach

    </body>
</html>