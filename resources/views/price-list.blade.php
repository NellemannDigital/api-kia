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
        @if($car->price_list->campaign?->valid_from
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
        <section class="mx-auto max-w-[210mm]">

            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl">
                    Modelprogram
                </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>
            
            <div class="text-xs text-primary">
                @php
                    $headers = [
                        ['label' => 'Udstyrsvariant', 'col' => 'w-[130px] text-left'],
                        ['label' => 'Ydelse', 'col' => 'w-[35px] text-center'],
                        ['label' => 'Batteri', 'col' => 'col-span-2 text-center'],
                        ['label' => 'Rækkevidde*', 'col' => 'col-span-2 text-center'],
                        ['label' => 'Forbrug*', 'col' => 'col-span-2 text-center'],
                        ['label' => 'Normalopladning <br><span class="font-light text-[9px]">(AC 0-100%)</span>', 'col' => 'col-span-3 text-center'],
                        ['label' => 'Hurtigopladning <br><span class="font-light text-[9px]">(DC 10-80%)</span>', 'col' => 'col-span-3 text-center'],
                        ['label' => 'Halvårlig CO<sub>2</sub>-afgift', 'col' => 'col-span-2 text-center'],
                        ['label' => 'Pris', 'col' => 'col-span-3 text-center'],
                    ];
                @endphp

                <div class="bg-primary p-2 rounded">
                    <div class="flex gap-2 items-center text-white font-bold leading-tight">
                        <span class="w-[130px] text-left">Udstyrsvariant</span>
                        <span class="w-[50px] text-center">Drivaksel</span>
                        <span class="w-[50px] text-center">Ydelse</span>
                        <span class="w-[55px] text-center">Batteri</span>
                        <span class="w-[75px] text-center">Rækkevidde*</span>
                        <span class="w-[65px] text-center">Forbrug*</span>
                        <span class="w-[100px] text-center">Normalopladning <br><span class="font-light text-[9px]">(AC 0-100%)</span></span>
                        <span class="w-[100px] text-center">Hurtigopladning <br><span class="font-light text-[9px]">(DC 10-80%)</span></span>
                        <span class="w-[60px] text-center">Halvårlig CO<sub>2</sub>-afgift</span>
                        <span class="w-[60px] text-center">
                            Pris 
                            @if($car->campaign_disclaimer)
                                **
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
                        @endphp

                        <div class="flex gap-2 items-center p-2 border-b border-primary-low">

                            <div class="w-[130px] text-left">
                                {{ $engine->name ?? '-' }}
                            </div>

                            <div class="w-[50px] text-center">
                                {{ $engine->drive }}
                            </div>

                            <div class="w-[50px] text-center">
                                {{ $engine->horse_power ? $engine->horse_power.' hk' : '-' }}
                            </div>

                            <div class="w-[55px] text-center">
                                {{ $tech->battery_size ? $tech->battery_size.' kWh' : '-' }}
                            </div>

                            <div class="w-[75px] text-center">
                                {{ $configTech?->pure_electric_range ? $configTech->pure_electric_range.' km' : '-' }}
                            </div>

                            <div class="w-[65px] text-center">
                                {{ $configTech?->consumption?->number ? $configTech->consumption->number.' Wh/km' : '-' }}
                            </div>

                            <div class="w-[100px] text-center">
                                {{ $tech->ac_charging_speed && $tech->ac_charging_time
                                    ? $tech->ac_charging_speed.' kW / '. formatTimeString($tech->ac_charging_time)
                                    : '-' }}
                            </div>

                            <div class="w-[100px] text-center">
                                {{ $tech->dc_charging_speed && $tech->dc_charging_time
                                    ? $tech->dc_charging_speed.' kW / '. formatTimeString($tech->dc_charging_time)
                                    : '-' }}
                            </div>

                            <div class="w-[60px] text-center">
                                {{ $configTech?->owner_tax ? $configTech->owner_tax.' kr.' : '-' }}
                            </div>

                            <div class="w-[60px] text-center">
                                {!! $price && $price->campaign_retail_price != 0
                                    ? Number::format($price->campaign_retail_price, locale: 'da') . ' kr. <br>' . '<span class="text-gray-400 line-through">' . Number::format($price->suggested_retail_price, locale: 'da') .' kr. </span>'
                                    : Number::format($price->suggested_retail_price, locale: 'da') .' kr.' !!}
                            </div>

                        </div>

                    @endforeach

                @endforeach
            </div>

            <div class="space-y-2 text-xs text-primary mt-4">
                @php
                    $priceListYear = compliance_text_for(['car' => $car], 'price_list_year');
                    $priceListDelivery = compliance_text_for(['car' => $car], 'price_list_delivery');
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

                <div>
                    <div class="bg-primary p-2 rounded">
                        <p class="font-bold text-white">Serviceinterval</p>
                    </div>

                    <div class="divide-y divide-primary-low border-b border-primary-low">
                        <div class="p-2">
                            <p>24 måneder / 30.000 km</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-2 text-xs text-primary mt-10">
                @php
                    $priceListDisclaimer = compliance_text_for(['car' => $car], 'price_list_disclaimer');
                @endphp

                @if($priceListDisclaimer)
                    <div>
                        {{ $priceListDisclaimer }}
                    </div>
                @endif

                <div class="text-xs text-primary">Prisliste udskrevet pr. {{ date('d-m-Y')}}</div>
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

            <div class="bg-primary p-2 rounded">
                <div class="gap-2 grid grid-cols-12 text-xs text-white font-bold">
                    <div class="col-span-4">
                        <p class="font-bold">Farve</p>
                    </div>
                    <div class="flex justify-end col-span-8">
                        @foreach ($trims as $trim)
                            <div class="w-24">
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
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="text-xs text-primary divide-y divide-primary-low">
                @foreach ($colorMatrix as $row)
                    <div class="grid grid-cols-12 gap-2 p-2 items-center">
                        <div class="col-span-4 flex items-center gap-2">
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
                            <span class="mt-0.75">{{ $row['colors']->primary_color }}</span>
                        </div>
                        <div class="col-span-8 flex justify-end">
                            @foreach ($row['prices'] as $price)
                                <div class="w-24 text-center">{{ $price ? Number::format($price, locale: 'da').' kr.' : '-' }}</div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="space-y-2 text-xs text-primary mt-4">
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

            <div class="gap-6 grid grid-cols-4">
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
                            <p class="text-primary text-xs text-center leading-tight">{{ $row['colors']->primary_color }}</p>
                        </div>
                    @endforeach
                </div>
        </section>

        @pageBreak

        <!-- Extra Equipment Packages-->
        <section class="mx-auto max-w-[210mm]">

            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl">
                    Ekstraudstyr
                </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>

            <div class="bg-primary p-2 rounded">
                <div class="gap-2 grid grid-cols-12 text-xs text-white font-bold">
                    <div class="col-span-4">
                        <p class="font-bold">Ekstraudstyr</p>
                    </div>
                    <div class="flex justify-end col-span-8">
                        @foreach ($trims as $trim)
                            <div class="w-24">
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
                                </p>
                            </div>
                        @endforeach
                    </div>
            </div>
            </div>
            <div class="text-xs text-primary divide-y divide-primary-low border-b border-primary-low">
                @foreach ($extraEquipmentPackageMatrix as $row)
                    <div class="grid grid-cols-12 gap-2 p-2 items-center">

                        <div class="col-span-4">
                            <span class="font-bold">
                                {{ $row['extraEquipmentPackages']->name }}
                            </span>

                            @if($row['extraEquipmentPackages']->equipment->isNotEmpty())
                                <ul class="mt-1 pl-4 list-disc list-outside">
                                    @foreach($row['extraEquipmentPackages']->equipment as $equipment)
                                        <li>{{ $equipment->name }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="col-span-8 flex justify-end">
                            @foreach ($trims as $trim)
                                <div class="w-24 text-center">

                                    @if($row['included'][$trim->id])
                                        S
                                    @else
                                        {{ $row['prices'][$trim->id]
                                            ? Number::format($row['prices'][$trim->id], locale: 'da').' kr.'
                                            : '-' }}
                                    @endif

                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach
            </div>
            <div class="space-y-2 text-xs text-primary mt-4">
                @php
                    $priceListExtrasDependency = compliance_text_for(['car' => $car], 'price_list_extras_dependency');
                    $priceListExtrasWltp = compliance_text_for(['car' => $car], 'price_list_extras_wltp');
                    $priceListExtrasTax = compliance_text_for(['car' => $car], 'price_list_extras_tax');
                    $priceListExtrasHighTax = compliance_text_for(['car' => $car], 'price_list_extras_high_tax');
                @endphp

                <div>S = Standardudstyr</div>

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

        <section class="mx-auto max-w-[210mm]">

            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl">
                    Fælge
                </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>

            <div class="grid grid-cols-3 gap-4">
                @foreach($groupedEquipment['Fælge'] as $equipment) 
                    @if($equipment->images->first())
                        <div class="flex flex-col rounded-lg overflow-hidden border border-primary-low">
                            <img src="{{ $equipment->images->first()->url }}?width=300"
                                alt="{{ $equipment->name }}"
                                class="w-full h-48 object-contain p-4" />

                            <div class="p-4 flex flex-col flex-1 border-t border-primary-low">

                                <div class="font-bold text-xs line-clamp-2">
                                    {{ $equipment->name }} (standard)
                                </div>

                                <div class="mt-auto flex flex-wrap gap-1 pt-2">
                                    @foreach($equipment->trim_names as $trim_name)
                                        <span class="inline-flex items-center rounded-full bg-primary-lowest px-2 py-0.5 text-[9px] text-primary">
                                            {{ $trim_name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                @foreach($groupedExtraEquipmentPackages['Fælge'] as $package) 
                    <div class="flex flex-col rounded-lg overflow-hidden border border-primary-low">
                        <img src="{{ $package->image->url }}?width=300"
                            alt="{{ $package->name }}"
                            class="w-full h-48 object-contain p-4" />

                        <div class="p-4 flex flex-col flex-1 border-t border-primary-low">

                            <div class="font-bold text-xs line-clamp-2">
                                {{ $package->name }} (tilvalg)
                            </div>

                            <div class="mt-auto flex flex-wrap gap-1 pt-2">
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
        </section>

        @pageBreak

        <section class="mx-auto max-w-[210mm]">

            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl">
                    Interiør
                </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>

            <div class="grid grid-cols-3 gap-4">
                @foreach($trims as $trim) 
                    @if($trim->interior->image)
                        <div class="flex flex-col rounded-lg overflow-hidden">
                            <img src="{{ $trim->interior->image->url }}?width=300"
                                alt="{{ $trim->interior->name }}"
                                class="w-full h-48 object-cover" />

                            <div class="p-4 flex flex-col flex-1 rounded-b-lg border-b border-x border-primary-low">

                                <div class="font-bold text-xs line-clamp-2">
                                    {{ $trim->interior->name }} (standard)
                                </div>

                                <div class="mt-auto flex flex-wrap gap-1 pt-2">
                                    <span class="inline-flex items-center rounded-full bg-primary-lowest px-2 py-0.5 text-[9px] text-primary">
                                        {{ $trim->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                @foreach($groupedExtraEquipmentPackages['Interiørfarve'] as $package) 
                    <div class="flex flex-col rounded-lg overflow-hidden">
                        <img src="{{ $package->image->url }}?width=300"
                            alt="{{ $package->name }}"
                            class="w-full h-48 object-cover" />

                        <div class="p-4 flex flex-col flex-1 rounded-b-lg border-b border-x border-primary-low">

                            <div class="font-bold text-xs line-clamp-2">
                                {{ $package->name }} (tilvalg)
                            </div>

                            <div class="mt-auto flex flex-wrap gap-1 pt-2">
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
        </section>

        @pageBreak

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

                            <ul class="columns-3 mt-2 pl-4 list-disc list-outside">
                                @foreach ($filteredEquipments as $equipment)
                                    <li class="pe-4 text-[10px] text-primary">{{ $equipment->name }}</li>

                                    @php
                                        $shownEquipmentCodes[$equipment->code] = true;
                                    @endphp
                                @endforeach
                            </ul>
                        </div>
                    @endif

                @endforeach

                <div class="bg-primary p-2 rounded mt-6">
                    <div class="gap-2 grid grid-cols-12 text-xs text-white font-bold">
                        <div class="col-span-4 font-bold">
                            Ekstraudstyr
                        </div>
                            <div class="col-span-8 flex justify-end font-bold">
                            <div class="w-24 text-center">Pris *</div>
                        </div>
                    </div>
                </div>
                <div class="text-xs text-primary divide-y divide-primary-low border-b border-primary-low">
                    @foreach ($trim->extraEquipmentPackages as $package)
                        <div class="grid grid-cols-12 gap-2 p-2 items-center">
                            <div class="col-span-8">
                                <span class="font-bold">{{ $package->name }}</span>

                                @if(!$package->equipment->isEmpty())
                                    <ul class="mt-1 pl-4 list-disc list-outside">
                                        @foreach($package->equipment as $equipment)
                                            <li>{{ $equipment->name }}</li>
                                        @endforeach
                                    </ul>
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
                                    <div class="mt-2 text-xs">
                                        Kræver: {{ $requires->implode(', ') }}
                                    </div>
                                @endif

                                @if($excludes->isNotEmpty())
                                    <div class="mt-2 text-xs">
                                        Udelukker: {{ $excludes->implode(', ') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-span-4 flex justify-end">
                                <div class="w-24 text-center"> 
                                    @php
                                    $price = $package->latestPrice?->suggested_retail_price;
                                @endphp

                                {{ $price && $price != 0
                                    ? Number::format($price, locale: 'da').' kr.'
                                    : '-' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="space-y-2 text-xs text-primary mt-4">
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

                @php
                    $previousTrim = $trim;
                @endphp

                @pageBreak

            </section>
        @endforeach
    </body>
</html>