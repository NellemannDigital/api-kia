<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ 'Priser - ' . $car->name }}</title>

        @vite(['resources/css/pdf.css'])
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
                </div>

                @if($car->campaign?->image?->url)
                    <img src="{{ $car->campaign?->image?->url }}?width=175" width="175" height="175"
                        class="bottom-8 left-8 absolute">
                @endif

                <img src="{{ asset('images/motif.svg') }}" class="top-0 left-0 absolute w-full">
            </div>
        </section>

        @pageBreak

        <!-- Campaign -->
        @if($car->price_list?->campaign?->valid_from
            && $car->price_list->campaign?->valid_to
            && today()->between(
                $car->price_list->campaign?->valid_from,
                $car->price_list->campaign?->valid_to
            )
        )
            <section class="mx-auto w-[210mm] h-[301mm]">
                <div class="relative w-full h-full">
                    <img src="{{ $car->price_list->campaign->image->url }}" class="w-full h-full object-cover rounded-lg">
                </div>
            </section>

            @pageBreak

        @endif

        <!-- Models & Prices -->

        @php
            $isB2b = $car->variant->b2b;

            $columnWidths = [
                'variant' => $isB2b ? 'w-[140px]' : 'w-[160px]',
                'power' => 'w-[50px]',
                'battery' => 'w-[55px]',
                'range' => 'w-[75px]',
                'consumption' => 'w-[65px]',
                'ac' => 'w-[100px]',
                'dc' => 'w-[100px]',
                'tax' => 'w-[60px]',
                'price' => $isB2b ? 'w-[150px]' : 'w-[60px]',
            ];
        @endphp

        <section class="mx-auto max-w-[210mm]">

            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl">
                    Modelprogram
                </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>
            
            <div class="text-xs text-primary">
                <div class="bg-primary p-2 rounded">
                    <div class="flex gap-2 items-center text-white font-bold leading-tight">
                        <span class="{{ $columnWidths['variant'] }} text-left">Udstyrsvariant</span>
                        <span class="{{ $columnWidths['power'] }} text-center">Ydelse</span>
                        <span class="{{ $columnWidths['battery'] }} text-center">Batteri</span>
                        <span class="{{ $columnWidths['range'] }} text-center">Rækkevidde*</span>
                        <span class="{{ $columnWidths['consumption'] }} text-center">Forbrug*</span>
                        <span class="{{ $columnWidths['ac'] }} text-center">Normalopladning <br><span class="font-light text-[9px]">(AC {{ $acChargingPercentage }}%)</span></span>
                        <span class="{{ $columnWidths['dc'] }} text-center">Hurtigopladning <br><span class="font-light text-[9px]">(DC {{ $dcChargingPercentage }}%)</span></span>
                        <span class="{{ $columnWidths['tax'] }} text-center">Halvårlig CO<sub>2</sub>-afgift</span>
                        <span class="{{ $columnWidths['price'] }} text-center">
                            Pris 
                            @if($car->campaign_disclaimer)
                                **
                            @endif

                            @if($isB2b)
                                <div class="grid grid-cols-2 gap-1 text-[9px] font-light">
                                    <span>ekskl. moms</span>
                                    <span>inkl. moms</span>
                                </div>
                            @endif
                        </span>
                    </div>
                </div>

                @foreach ($trims as $trim)

                    <div class="mt-2 p-2 border-b border-primary-low font-bold">
                        {{ $trim->name }}
                    </div>

                    @foreach ($trim->powertrains as $powertrain)

                        @php
                            $engine = $powertrain->engine;
                            $tech = $powertrain->technical_specifications;
                            $configTech = $powertrain->configuration?->technical_specifications;
                            $price = $powertrain->prices->last();

                            $campaign = $price?->campaign_retail_price;
                            $hasCampaign = $campaign && $campaign != 0;
                        
                            $suggested = $price?->suggested_retail_price;

                            if ($isB2b) {
                                $vanPrice = $price?->van_price;
                                $vanPriceVat = $price?->van_price_vat;
                            }
                        @endphp

                        <div class="flex gap-2 items-center p-2 border-b border-primary-low">

                            <div class="{{ $columnWidths['variant'] }} text-left">
                                {{ $engine->name ?? '-' }} {{ $engine->drive ? '(' . $engine->drive . ')' : '' }}
                            </div>

                            <div class="{{ $columnWidths['power'] }} text-center">
                                {{ $engine->horse_power ? $engine->horse_power.' hk' : '-' }}
                            </div>

                            <div class="{{ $columnWidths['battery'] }} text-center">
                                {{ $tech->battery_size ? $tech->battery_size.' kWh' : '-' }}
                            </div>

                            <div class="{{ $columnWidths['range'] }} text-center">
                                {{ $configTech?->pure_electric_range ? $configTech->pure_electric_range.' km' : '-' }}
                            </div>

                            <div class="{{ $columnWidths['consumption'] }} text-center">
                                {{ $configTech?->consumption?->number ? $configTech->consumption->number.' Wh/km' : '-' }}
                            </div>

                            <div class="{{ $columnWidths['ac'] }} text-center">
                                {{ $tech->ac_charging_speed && $tech->ac_charging_time
                                    ? $tech->ac_charging_speed.' kW / '. formatTimeString($tech->ac_charging_time)
                                    : '-' }}
                            </div>

                            <div class="{{ $columnWidths['dc'] }} text-center">
                                {{ $tech->dc_charging_speed && $tech->dc_charging_time
                                    ? $tech->dc_charging_speed.' kW / '. formatTimeString($tech->dc_charging_time)
                                    : '-' }}
                            </div>

                            <div class="{{ $columnWidths['tax'] }} text-center">
                                {{ $configTech?->owner_tax ? $configTech->owner_tax.' kr.' : '-' }}
                            </div>
                           
                            <div class="{{ $columnWidths['price'] }} text-center">
                                
                                @if($isB2b)

                                    <div class="grid grid-cols-2 gap-1 text-center">
                                        <span class="block">
                                            {{ $vanPrice !== null
                                                ? Number::format($vanPrice, locale: 'da') . ' kr.'
                                                : '-' }}
                                        </span>

                                        <span class="block">
                                            {{ $vanPriceVat !== null
                                                ? Number::format($vanPriceVat, locale: 'da') . ' kr.'
                                                : '-' }}
                                        </span>
                                    </div>

                                @else

                                    @if($hasCampaign)
                                        {!! Number::format($campaign, locale: 'da') !!} kr. <br>
                                        <span class="text-gray-400 line-through">
                                            {{ $suggested !== null
                                            ? Number::format($suggested, locale: 'da') . ' kr.'
                                            : '-' }}
                                        </span>
                                    @else
                                        {{ $suggested !== null
                                        ? Number::format($suggested, locale: 'da') . ' kr.'
                                        : '-' }}
                                    @endif

                                @endif
                            </div>

                        </div>

                    @endforeach

                @endforeach
            </div>

            <div class="space-y-2 text-[10px] text-primary mt-4">
                @php
                    $priceListYear = compliance_text_for(['car' => $car], 'price_list_year');
                    $priceListDelivery = $isB2b ? compliance_text_for(['car' => $car], 'price_list_delivery_van') : compliance_text_for(['car' => $car], 'price_list_delivery');
                    $priceListWltp = compliance_text_for(['car' => $car], 'price_list_wltp');
                @endphp

                @if($priceListYear || $priceListDelivery)
                    <div>
                        {{ $priceListYear }}
                        {{ $priceListDelivery }}
                    </div>
                @endif

                @if($priceListWltp)
                    <div>
                        * {{ $priceListWltp }}
                    </div>
                @endif

                @if($car->campaign_disclaimer)
                    <div>
                        ** {{ $car->campaign_disclaimer }}
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-8 text-xs text-primary mt-10">
                @if(count($car->warranties) > 0)
                    <div>
                        <div class="bg-primary p-2 rounded">
                            <p class="font-bold text-white">Garantier</p>
                        </div>

                        <div class="divide-y divide-primary-low border-b border-primary-low">
                            @php
                                $warranties = $car->warranties['primary']
                            @endphp
                            
                            <div class="grid grid-cols-2 p-2">
                                <p>Garanti</p>
                                <p>{{ $warranties->base_warranty }}</p>
                            </div>

                            <div class="grid grid-cols-2 p-2">
                                <p>Batterigaranti</p>
                                <p>{{ $warranties->hv_battery_warranty }}</p>
                            </div>

                            <div class="grid grid-cols-2 p-2">
                                <p>Lakgaranti</p>
                                <p>{{ $warranties->paint_warranty }}</p>
                            </div>

                            <div class="grid grid-cols-2 p-2">
                                <p>Gennemtæringsgaranti</p>
                                <p>{{ $warranties->corrosion_warranty }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(count($car->service_intervals) > 0)
                    <div>
                        <div class="bg-primary p-2 rounded">
                            <p class="font-bold text-white">Serviceinterval</p>
                        </div>

                        <div class="divide-y divide-primary-low border-b border-primary-low">
                            <div class="p-2">
                                <p>{{ $car->service_intervals[0]->months }} måneder / {{ Number::format($car->service_intervals[0]->kilometers, locale: 'da')  }} km</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-2 text-[10px] text-primary mt-10">
                @php
                    $priceListDisclaimer = compliance_text_for(['car' => $car], 'price_list_disclaimer');
                @endphp

                @if($priceListDisclaimer)
                    <div>
                        {{ $priceListDisclaimer }}
                    </div>
                @endif

                <div class="text-[10px] text-primary">Prisliste udskrevet pr. {{ date('d-m-Y')}}</div>
            </div>

       </section>

        @pageBreak

        <!-- Colors & Prices -->
        <section class="mx-auto max-w-[210mm]">

            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl">
                    Farver
                </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>

            <div class="bg-primary p-2 rounded flex justify-between items-center gap-2 font-bold text-white text-xs">

                <p class="text-xs text-white font-bold">Farve</p>

                <div class="flex justify-end gap-4">
                    @foreach ($trims as $trim)
                        <div @class(['w-32' => $isB2b, 'w-16' => ! $isB2b ])>
                            <p class="text-center">
                                {{ $trim->name }}

                                @if ($trim->uses_high_tax)
                                    @php
                                        $usesHighTax = true
                                    @endphp

                                    **
                                @else
                                    @php
                                        $usesHighTax = false
                                    @endphp

                                    *
                                @endif

                                @if($isB2b)
                                    <div class="grid grid-cols-2 gap-1 text-[9px] font-light text-center">
                                        <span>ekskl. moms</span>
                                        <span>inkl. moms</span>
                                    </div>
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="text-xs text-primary divide-y divide-primary-low border-b border-primary-low">

                @foreach ($colorMatrix as $row)

                    <div class="flex justify-between p-2 items-center">

                        <div class="flex items-center gap-2">
                            @if($row['colors']->color_image?->url)
                                <img src="{{ $row['colors']->color_image->url }}" class="rounded-full w-4 h-4">
                            @else
                                <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <pattern id="diagonal-stripes" patternUnits="userSpaceOnUse" width="4" height="4" patternTransform="rotate(45)">
                                        <rect width="2" height="4" fill="#ccc" />
                                        </pattern>
                                    </defs>
                                    <circle cx="8" cy="8" r="8" fill="url(#diagonal-stripes)" />
                                </svg>
                            @endif
                            <span class="mt-0.75">{{ $row['colors']->primary_color }} {{ $row['colors']->secondary_color ? '/ ' . $row['colors']->secondary_color : ''  }}</span>
                        </div>

                        <div class="gap-4 flex justify-end items-center">
                            @foreach ($row['prices'] as $price)
                                <div @class(['w-32 text-center' => $isB2b, 'w-16 text-center' => ! $isB2b ])>

                                    @if($isB2b)
                                       <div class="grid grid-cols-2 gap-1 text-center">
                                            <span class="block">
                                                {{ $price['priceExVat'] ? Number::format($price['priceExVat'], locale: 'da').' kr.' : '-' }}
                                            </span>

                                            <span class="block">
                                               {{ $price['price'] ? Number::format($price['price'], locale: 'da').' kr.' : '-' }}
                                            </span>
                                        </div>
                                    @else

                                        @if( $price['campaignPrice'])
                                            
                                            {!! Number::format($price['campaignPrice'], locale: 'da') !!} kr. <br>
                                            
                                            <span class="text-gray-400 line-through">
                                                {{ $price['price'] ? Number::format($price['price'], locale: 'da').' kr.' : '-' }}
                                            </span>
                                        @else

                                            {{ $price['price'] ? Number::format($price['price'], locale: 'da').' kr.' : '-' }}

                                        @endif

                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                    </div>

                @endforeach
            </div>

            <div class="space-y-2 text-[10px] text-primary mt-4">
                @php
                    $priceListMattColorDisclaimer = compliance_text_for(['car' => $car], 'price_list_matt_color_disclaimer');
                    $priceListColorTax = compliance_text_for(['car' => $car], 'price_list_color_tax');
                    $priceListColorHighTax = compliance_text_for(['car' => $car], 'price_list_color_high_tax');
                @endphp

                @if($priceListMattColorDisclaimer)
                    <div>
                        {{ $priceListMattColorDisclaimer }}
                    </div>
                @endif

                @if($priceListColorTax)
                    <div>
                        * {{ $priceListColorTax }}
                    </div>
                @endif

                @if($usesHighTax && $priceListColorHighTax)
                    <div>
                        ** {{ $priceListColorHighTax }}
                    </div>
                @endif
            </div>

            <div class="gap-6 grid grid-cols-4 mt-6">
                   @foreach ($colorMatrix as $row)
                        <div>
                            @if(!empty($row['colors']->turntable_images[0]['url']))
                                <img src="{{ $row['colors']->turntable_images[0]['url'] }}?width=200" class="object-contain aspect-video">
                            @else
                                <div class="w-full aspect-video pb-4 flex justify-center items-end">
                                    <div class="w-[65%] h-[80%] bg-primary-lowest flex items-center rounded justify-center text-primary-mid">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-image-icon lucide-image">
                                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                            <circle cx="9" cy="9" r="2"/>
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                            <p class="text-primary text-xs text-center leading-tight">{{ $row['colors']->primary_color }} {{ $row['colors']->secondary_color ? '/ ' . $row['colors']->secondary_color : ''  }}</p>
                        </div>
                    @endforeach
                </div>
        </section>

        @pageBreak

        @if($extraEquipmentPackageMatrix->isNotEmpty())

            <!-- Extra Equipment Packages-->
            <section class="mx-auto max-w-[210mm]">

                <div class="flex justify-between items-center mb-3">
                    <div class="font-bold text-xl">
                        Ekstraudstyr
                    </div>
                    <img src="{{ asset('images/logo.png') }}" class="block w-20">
                </div>

                <div class="bg-primary p-2 rounded flex justify-between items-center gap-2 font-bold text-white text-xs">

                    <p class="text-xs text-white font-bold">Ekstraudstyr</p>

                    <div class="flex justify-end gap-4">
                        @foreach ($trims as $trim)
                            <div @class(['w-32' => $isB2b, 'w-16' => ! $isB2b ])>
                                <p class="text-center">
                                    {{ $trim->name }}

                                    @if ($trim->uses_high_tax)
                                        @php
                                            $usesHighTax = true
                                        @endphp

                                        **
                                    @else
                                        @php
                                            $usesHighTax = false
                                        @endphp

                                        *
                                    @endif

                                    @if($isB2b)
                                        <div class="grid grid-cols-2 gap-1 text-[9px] font-light text-center">
                                            <span>ekskl. moms</span>
                                            <span>inkl. moms</span>
                                        </div>
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="text-xs text-primary divide-y divide-primary-low border-b border-primary-low">
                    @foreach ($extraEquipmentPackageMatrix as $row)

                    @php
                        $disclaimers = collect($trims)
                            ->map(function ($trim) use ($row) {
                                $text = $row['prices'][$trim->id]['disclaimer'] ?? null;

                                if (! $text) {
                                    return null;
                                }

                                return [
                                    'trim' => $trim,
                                    'text' => $text,
                                ];
                            })
                            ->filter();
                    @endphp

                        <div class="flex justify-between p-2 items-center">

                            <div class="space-y-1">
                                <div class="font-bold"> {{ $row['extraEquipmentPackages']->name }}</div>

                                @if($row['extraEquipmentPackages']->equipment->isNotEmpty())

                                    @php
                                        $singleEquipment = $row['extraEquipmentPackages']->equipment->count() === 1
                                            ? $row['extraEquipmentPackages']->equipment->first()
                                            : null;
                                    @endphp

                                    @if($singleEquipment && $singleEquipment->name === $row['extraEquipmentPackages']->name)
                                        
                                    @else
                                        <ul class="pl-4 list-disc list-outside text-[9px]">
                                            @foreach($row['extraEquipmentPackages']->equipment as $equipment)
                                                <li>{{ $equipment->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                @endif

                                @if($disclaimers->isNotEmpty())
                                    <div class="text-[9px] text-gray-500">
                                        @foreach($disclaimers as $disclaimer)
                                            <div>
                                                * {{ $disclaimer['trim']->name }}: {{ $disclaimer['text'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            </div>

                            <div class="gap-4 flex justify-end items-center">
                                @foreach ($trims as $trim)
                                    <div @class(['w-32 text-center' => $isB2b, 'w-16 text-center' => ! $isB2b ])>
                                        
                                        @php
                                            $hasDisclaimer = filled($row['prices'][$trim->id]['disclaimer'] ?? null);
                                        @endphp

                                        @if($isB2b)
                                        <div class="grid grid-cols-2 gap-1 text-center">
                                                <span class="block">
                                                    {{ $row['prices'][$trim->id]['priceExVat']
                                                    ? Number::format($row['prices'][$trim->id]['priceExVat'], locale: 'da').' kr.'
                                                    : '-' }}

                                                    @if($hasDisclaimer)
                                                        <sup>*</sup>
                                                    @endif
                                                </span>

                                                <span class="block">
                                                {{ $row['prices'][$trim->id]['price']
                                                    ? Number::format($row['prices'][$trim->id]['price'], locale: 'da').' kr.'
                                                    : '-' }}

                                                    @if($hasDisclaimer)
                                                        <sup>*</sup>
                                                    @endif
                                                </span>
                                            </div>
                                        @else

                                            @if( $row['prices'][$trim->id]['campaignPrice'])
                                                
                                                {!! Number::format($row['prices'][$trim->id]['campaignPrice'], locale: 'da') !!} kr. <br>
                                                
                                                <span class="text-gray-400 line-through">
                                                    {{ $row['prices'][$trim->id]['price']
                                                        ? Number::format($row['prices'][$trim->id]['price'], locale: 'da').' kr.'
                                                        : '-' }}

                                                        @if($hasDisclaimer)
                                                            <sup>*</sup>
                                                        @endif
                                                </span>
                                            @else

                                                {{ $row['prices'][$trim->id]['price']
                                                    ? Number::format($row['prices'][$trim->id]['price'], locale: 'da').' kr.'
                                                    : '-' }}

                                                    @if($hasDisclaimer)
                                                        <sup>*</sup>
                                                    @endif
                                            @endif

                                        @endif

                                    </div>
                                @endforeach

                            </div>

                        </div>
                    @endforeach
                </div>
                <div class="space-y-2 text-[10px] text-primary mt-4">
                    @php
                        $priceListExtrasDependency = compliance_text_for(['car' => $car], 'price_list_extras_dependency');
                        $priceListExtrasWltp = compliance_text_for(['car' => $car], 'price_list_extras_wltp');
                        $priceListExtrasTax = compliance_text_for(['car' => $car], 'price_list_extras_tax');
                        $priceListExtrasHighTax = compliance_text_for(['car' => $car], 'price_list_extras_high_tax');
                    @endphp

                    @if($priceListExtrasDependency)
                        <div>
                            {{ $priceListExtrasDependency }}
                        </div>
                    @endif

                    @if($priceListExtrasWltp)
                        <div>
                            {{ $priceListExtrasWltp }}
                        </div>
                    @endif

                    @if($priceListExtrasTax)
                        <div>
                            * {{ $priceListExtrasTax }}
                        </div>
                    @endif

                    @if($usesHighTax && $priceListExtrasHighTax)
                        <div>
                            ** {{ $priceListExtrasHighTax }}
                        </div>
                    @endif
                </div>
            </section>

            @pageBreak

        @endif

        <!-- Interior & exterior -->

        @php
            $alloyWheelsCount =
                collect($groupedEquipment['Fælge'] ?? [])->count()
                + collect($groupedExtraEquipmentPackages['Fælge'] ?? [])->count();

            $splitInteriorToNextPage = $alloyWheelsCount > 9;

            $alloyWheels = $alloyWheelsCount > 0;

            $hasInteriorImages = $trims->contains(function ($trim) {
                return !empty($trim->interior?->image);
            });

            $interiors =
                $hasInteriorImages
                || !empty($groupedExtraEquipmentPackages['Interiørfarve'] ?? null);
        @endphp

        @if($alloyWheels || $interiors)

            <section class="mx-auto max-w-[210mm] space-y-8">

                <div class="flex justify-between items-center mb-3">
                    <div class="font-bold text-xl">
                        Interiør & eksteriør
                    </div>
                    <img src="{{ asset('images/logo.png') }}" class="block w-20">
                </div>

                @if($alloyWheels)

                    <div class="space-y-4">

                        <div class="bg-primary p-2 rounded">
                            <p class="font-bold text-white text-xs">Alufælge</p>
                        </div>

                        <div class="grid grid-cols-3 gap-4">

                            @foreach(($groupedEquipment['Fælge'] ?? []) as $equipment)
                                <div class="flex flex-col rounded-lg overflow-hidden border border-primary-low">
                                    <img src="{{ $equipment->images->first()->url }}?width=300"
                                        alt="{{ $equipment->name }}"
                                        class="w-full h-38 object-contain p-4" />

                                    <div class="p-4 flex flex-col flex-1 border-t border-primary-low">

                                        <div class="font-bold text-xs line-clamp-2">
                                            {{ $equipment->name }} (standard)
                                        </div>

                                        <div class="mt-auto flex flex-wrap gap-1 pt-4">
                                            @foreach($equipment->trim_names as $trim_name)
                                                <span class="inline-flex items-center rounded-full bg-primary-lowest px-2 py-0.5 text-[9px] text-primary">
                                                    {{ $trim_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            
                            @foreach(($groupedExtraEquipmentPackages['Fælge'] ?? []) as $package) 
                                <div class="flex flex-col rounded-lg overflow-hidden border border-primary-low">
                                    <img src="{{ $package->image->url }}?width=300"
                                        alt="{{ $package->name }}"
                                        class="w-full h-38 object-contain p-4" />

                                    <div class="p-4 flex flex-col flex-1 border-t border-primary-low">

                                        <div class="font-bold text-xs line-clamp-2">
                                            {{ $package->name }} (tilvalg)
                                        </div>

                                        <div class="mt-auto flex flex-wrap gap-1 pt-4">
                                            @foreach($package->trim_names as $trim_name)
                                                <span class="inline-flex items-center rounded-full bg-primary-lowest px-2 py-0.5 text-[9px] text-primary">
                                                    {{ $trim_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                    </div>
                    
                @endif

                @if($interiors)

                @if($splitInteriorToNextPage)
                    @pageBreak
                @endif

                <div class="space-y-4">
                    <div class="bg-primary p-2 rounded">
                        <p class="font-bold text-white text-xs">Sædebetræk</p>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        @php
                            $grouped = $trims
                                ->filter(fn ($trim) => $trim->interior?->image)
                                ->groupBy(fn ($trim) => $trim->interior->image->url);
                        @endphp

                        @foreach($grouped as $imageId => $group)
                            @php
                                $first = $group->first();
                                $interior = $first->interior;
                            @endphp

                            <div class="flex flex-col rounded-lg overflow-hidden">
                                <img src="{{ $interior->image?->url }}?width=300"
                                    alt="{{ $interior->name }}"
                                    class="w-full h-38 object-cover" />

                                <div class="p-4 flex flex-col flex-1 rounded-b-lg border-b border-x border-primary-low">

                                    <div class="font-bold text-xs line-clamp-2">
                                        {{ $interior->name }} (standard)
                                    </div>

                                    <div class="mt-auto flex flex-wrap gap-1 pt-4">
                                        @foreach($group as $trim)
                                            <span class="inline-flex items-center rounded-full bg-primary-lowest px-2 py-0.5 text-[9px] text-primary">
                                                {{ $trim->name }}
                                            </span>
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                        @endforeach

                        @foreach(($groupedExtraEquipmentPackages['Interiørfarve'] ?? []) as $package) 
                            <div class="flex flex-col rounded-lg overflow-hidden">
                                <img src="{{ $package->image->url }}?width=300"
                                    alt="{{ $package->name }}"
                                    class="w-full h-38 object-cover" />

                                <div class="p-4 flex flex-col flex-1 rounded-b-lg border-b border-x border-primary-low">

                                    <div class="font-bold text-xs line-clamp-2">
                                        {{ $package->name }} (tilvalg)
                                    </div>

                                    <div class="mt-auto flex flex-wrap gap-1 pt-4">
                                        @foreach($package->trim_names as $trim_name)
                                            <span class="inline-flex items-center rounded-full bg-primary-lowest px-2 py-0.5 text-[9px] text-primary">
                                                {{ $trim_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>

                @endif

            </section>

            @pageBreak

        @endif

        <!-- Trims -->
        @php
            $previousTrim = null;
            $shownEquipmentCodes = [];
        @endphp

        @foreach($trims as $trim)
            <section class="mx-auto max-w-[210mm]">

                <div class="flex justify-between items-center mb-3">
                    <div class="font-bold text-xl">
                        Udstyr | {{ $trim->name }}
                    </div>
                    <img src="{{ asset('images/logo.png') }}" class="block w-20">
                </div> 

                <div class="bg-primary p-2 rounded">
                    <div class="gap-2 grid grid-cols-12 text-xs text-white font-bold">
                        <div class="col-span-4 font-bold">
                            Standardudstyr @if($previousTrim) <span class="font-normal"> - Udstyr fra {{ $previousTrim->name }} og... @endif<span>
                        </div>
                    </div>
                </div>

                @foreach ($trim->equipment->groupBy('category') as $category => $equipments)

                    @php
                        $filteredEquipments = $equipments
                            ->filter(fn($e) => !isset($shownEquipmentCodes[$e->code]))
                            ->sortBy('name');
                    @endphp

                    @if ($filteredEquipments->isNotEmpty())
                        <div class="p-2 border-primary-low border-b">
                            <div class="text-xs font-semibold text-primary">
                                {{ $category }}
                            </div>

                            <ul class="columns-3 mt-1 pl-4 list-disc list-outside">
                                @foreach ($filteredEquipments as $equipment)
                                    <li class="pe-4 text-[9px] text-primary">{{ $equipment->name }}</li>

                                    @php
                                        $shownEquipmentCodes[$equipment->code] = true;
                                    @endphp
                                @endforeach
                            </ul>
                        </div>
                    @endif

                @endforeach

                @if(count($trim->extraEquipmentPackages) > 0)
                    <div class="bg-primary p-2 rounded flex justify-between items-center gap-2 font-bold text-white text-xs mt-6">

                        <p class="text-xs text-white font-bold">Ekstraudstyr</p>

                        <div class="flex justify-end gap-4">
                            <div @class(['w-32' => $isB2b, 'w-16' => ! $isB2b ])>
                                <p class="text-center">
                                    Pris *

                                    @if($isB2b)
                                        <div class="grid grid-cols-2 gap-1 text-[9px] font-light text-center">
                                            <span>ekskl. moms</span>
                                            <span>inkl. moms</span>
                                        </div>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="text-xs text-primary divide-y divide-primary-low border-b border-primary-low">
                        @foreach ($trim->extraEquipmentPackages as $package)
                            <div class="flex items-center justify-between gap-2 p-2">
                                <div class="space-y-1">
                                    <div class="font-bold">{{ $package->name }}</div>

                                    @if(!$package->equipment->isEmpty())

                                        @php
                                            $singleEquipment = $package->equipment->count() === 1
                                                ? $package->equipment->first()
                                                : null;
                                        @endphp

                                        @if($singleEquipment && $singleEquipment->name === $package->name)
                                            
                                        @else
                                            <ul class="pl-4 list-disc list-outside text-[9px] mb-2">
                                                @foreach($package->equipment as $equipment)
                                                    <li>{{ $equipment->name }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif

                                    @php
                                        $requires = collect($package->requires ?? [])->pluck('0.name')
                                            ->merge(collect($package->engine_required ?? [])->pluck('name'))
                                            ->merge(collect($package->transmission_required ?? [])->pluck('name'))
                                            ->merge(collect($package->color_required ?? [])->pluck('primary_color')); 
                                    @endphp

                                    @php
                                        $excludes = collect($package->excludes ?? [])->pluck('name')
                                            ->merge(collect($package->excludes_standard_equipment ?? [])->pluck('name'));
                                    @endphp

                                    @if($requires->isNotEmpty())
                                        <div class="text-[9px]">
                                            Kræver: {{ $requires->implode(', ') }}
                                        </div>
                                    @endif

                                    @if($excludes->isNotEmpty())
                                        <div class="text-[9px]">
                                            Udelukker: {{ $excludes->implode(', ') }}
                                        </div>
                                    @endif

                                    @if($package->disclaimer)
                                        <div class="text-[9px]">
                                            {{ $package->disclaimer }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex justify-end">
                                    <div @class(['w-32 text-center' => $isB2b, 'w-16 text-center' => ! $isB2b ])>
                                        @php
                                            $price = $package->latestPrice?->suggested_retail_price;
                                            $campaignPrice = $package->latestPrice?->campaign_retail_price;
                                            $priceExVat = $package->latestPrice?->retail_price_ex_vat;
                                        @endphp

                                        @if($isB2b)
                                            <div class="grid grid-cols-2 gap-1 text-center">
                                                <span class="block">
                                                    {{ $priceExVat && $priceExVat != 0
                                                    ? Number::format($priceExVat, locale: 'da').' kr.'
                                                    : '-' }}
                                                </span>

                                                <span class="block">
                                                    {{ $price && $price != 0
                                                        ? Number::format($price, locale: 'da').' kr.'
                                                        : '-' }}
                                                </span>
                                            </div>

                                         @else

                                            @if($campaignPrice)
                                                {!! Number::format($campaignPrice, locale: 'da') !!} kr. <br>
                                                <span class="text-gray-400 line-through">
                                                    {{ $price && $price != 0
                                                        ? Number::format($price, locale: 'da').' kr.'
                                                        : '-' }}
                                                </span>
                                            @else
                                                {{ $price && $price != 0
                                                    ? Number::format($price, locale: 'da').' kr.'
                                                    : '-' }}
                                            @endif

                                        @endif

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 text-[10px] text-primary mt-4">
                        @php
                            $priceListExtrasWltp = compliance_text_for(['car' => $car], 'price_list_extras_wltp');
                            $priceListExtrasTax = compliance_text_for(['car' => $car], 'price_list_extras_tax');
                            $priceListExtrasHighTax = compliance_text_for(['car' => $car], 'price_list_extras_high_tax');
                        @endphp

                        @if($priceListExtrasWltp)
                            <div>
                                {{ $priceListExtrasWltp }}
                            </div>
                        @endif

                        @if($trim->uses_high_tax && $priceListExtrasHighTax)
                            <div>
                                * {{ $priceListExtrasHighTax }}
                            </div>
                        @elseif($priceListExtrasTax)
                            <div>
                                * {{ $priceListExtrasTax }}
                            </div>
                        @endif

                    </div>
                @endif

                @php
                    $previousTrim = $trim;
                @endphp

                @pageBreak

            </section>
        @endforeach
    </body>
</html>