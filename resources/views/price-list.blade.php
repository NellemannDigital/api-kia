<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <div class="top-9 left-21 absolute font-bold text-white text-6xl">{{ $car->name ?? '' }}</div>
                @if($car->campaign != null)
                    <img src="{{ $car->campaign->image->url }}?width=175" width="175" height="175" class="bottom-8 left-8 absolute">
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
            <section class="mx-auto w-[210mm] h-[305mm]">
                <div class="relative w-full h-full">
                    <img src="{{ $car->price_list->campaign->image->url }}" class="rounded-lg w-full h-full object-cover">
                </div>
            </section>

            @pageBreak

        @endif

        <!-- Models & Prices -->
        <section class="mx-auto max-w-4xl">
            
            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl"> Modelprogram </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>

            <div class="text-xs text-primary">
                @php
                    $headers = [
                        ['label' => 'Udstyrsvariant', 'col' => 'col-span-4 text-left'],
                        ['label' => 'Ydelse', 'col' => 'col-span-2 text-center'],
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
                    <div class="grid grid-cols-23 gap-2 items-center">
                        @foreach ($headers as $header)
                            <div class="{{ $header['col'] }}">
                                <p class="font-bold text-white leading-tight">{!! $header['label'] !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach ($car->trims as $trim)

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

                        <div class="grid grid-cols-23 gap-2 items-center p-2 border-b border-primary-low">

                            <div class="col-span-4 text-left">
                                {{ $engine->name ?? '-' }}
                            </div>

                            <div class="col-span-2 text-center">
                                {{ $engine->horse_power ? $engine->horse_power.' hk' : '-' }}
                            </div>

                            <div class="col-span-2 text-center">
                                {{ $tech->battery_size ? $tech->battery_size.' kWh' : '-' }}
                            </div>

                            <div class="col-span-2 text-center">
                                {{ $configTech?->pure_electric_range ? $configTech->pure_electric_range.' km' : '-' }}
                            </div>

                            <div class="col-span-2 text-center">
                                {{ $configTech?->consumption?->number ? $configTech->consumption->number.' Wh/km' : '-' }}
                            </div>

                            <div class="col-span-3 text-center">
                                {{ $tech->ac_charging_speed && $tech->ac_charging_time
                                    ? $tech->ac_charging_speed.' kW / '. formatTimeString($tech->ac_charging_time)
                                    : '-' }}
                            </div>

                            <div class="col-span-3 text-center">
                                {{ $tech->dc_charging_speed && $tech->dc_charging_time
                                    ? $tech->dc_charging_speed.' kW / '. formatTimeString($tech->dc_charging_time)
                                    : '-' }}
                            </div>

                            <div class="col-span-2 text-center">
                                {{ $configTech?->owner_tax ? $configTech->owner_tax.' kr.' : '-' }}
                            </div>

                            <div class="col-span-3 text-center">
                                {{ $price && $price->suggested_retail_price != 0
                                    ? Number::format($price->suggested_retail_price, locale: 'da').' kr.'
                                    : '-' }}
                            </div>

                        </div>

                    @endforeach

                @endforeach
            </div>

            <div class="space-y-2 text-xs text-primary mt-4">
                @foreach (['price', 'consumption', 'changes'] as $key)
                    @if(!empty($complianceTexts[$key]))
                        <div>{!! $complianceTexts[$key] !!}</div>
                    @endif
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-8 text-xs text-primary mt-10">
                <div>
                    <div class="bg-primary p-2 rounded">
                        <p class="font-bold text-white">Garantier</p>
                    </div>

                    <div class="divide-y divide-primary-low border-b border-primary-low">
                        <div class="grid grid-cols-2 p-2">
                            <p>Garanti</p>
                            <p>7 år / 150.000 km</p>
                        </div>

                        <div class="grid grid-cols-2 p-2">
                            <p>Batterigaranti</p>
                            <p>8 år / 160.000 km</p>
                        </div>

                        <div class="grid grid-cols-2 p-2">
                            <p>Lakgaranti</p>
                            <p>5 år / 150.000 km</p>
                        </div>

                        <div class="grid grid-cols-2 p-2">
                            <p>Gennemtæringsgaranti</p>
                            <p>12 år / ubegrænset km</p>
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

            <div class="text-xs text-primary mt-10">Prisliste udskrevet pr. {{ date('d-m-Y')}}</div>
        </section>


        <!-- Colors & Prices -->
        <section class="mx-auto max-w-4xl">

            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl"> Farver </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>

            <div>
                <div class="bg-primary p-2 rounded">
                    <div class="gap-2 grid grid-cols-12 text-xs text-white font-bold">
                        <div class="col-span-4">
                            <p class="font-bold">Farve</p>
                        </div>
                        <div class="flex justify-end col-span-8">
                            @foreach ($car->trims as $trim)
                                <div class="w-24">
                                    <p class="text-center">{{ $trim->name }}</p>
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
            </div>

            <div>
                <div class="gap-6 grid grid-cols-4">
                   @foreach ($colorMatrix as $row)
                        <div>
                            @if(!empty($row['colors']->turntable_images[0]['url']))
                                <img src="{{ $row['colors']->turntable_images[0]['url'] }}?width=200" class="object-contain aspect-video">
                            @else
                                <div class="w-full aspect-video pb-4 flex justify-center items-end">
                                    <div class="w-[65%] h-[80%] bg-primary-lowest rounded-lg flex items-center justify-center text-primary-mid">
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
            </div>
        </section>


        <!-- Extra Equipment Packages-->
        <section class="mx-auto max-w-4xl">

            <div class="flex justify-between items-center mb-3">
                <div class="font-bold text-xl"> Ekstraudstyr </div>
                <img src="{{ asset('images/logo.png') }}" class="block w-20">
            </div>

            <div>
                <div class="bg-primary p-2 rounded">
                    <div class="gap-2 grid grid-cols-12 text-xs text-white font-bold">
                        <div class="col-span-4">
                            <p class="font-bold">Ekstraudstyr</p>
                        </div>
                        <div class="flex justify-end col-span-8">
                            @foreach ($car->trims as $trim)
                                <div class="w-24">
                                    <p class="text-center">{{ $trim->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="text-xs text-primary divide-y divide-primary-low border-b border-primary-low">
                    @foreach ($extraEquipmentPackageMatrix as $row)
                        <div class="grid grid-cols-12 gap-2 p-2 items-center">
                            <div class="col-span-4">
                                <span class="font-bold">{{ $row['extraEquipmentPackages']->name }}</span>

                                @if(!$row['extraEquipmentPackages']->equipment->isEmpty())
                                    <ul class="mt-1 pl-4 list-disc list-outside">
                                        @foreach($row['extraEquipmentPackages']->equipment as $equipment)
                                            <li>{{ $equipment->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="col-span-8 flex justify-end">
                                @foreach ($car->trims as $trim)
                                    <div class="w-24 text-center">
                                        @php
                                            $price = $row['prices'][$trim->id] ?? null;

                                            $trimCodes = $trim->equipment->pluck('code')->toArray();
                                            $packageCodes = $row['extraEquipmentPackages']->equipment->pluck('code')->toArray();
                                            $included = !empty($packageCodes) && empty(array_diff($packageCodes, $trimCodes));
                                        @endphp

                                        @if($included)
                                            S
                                        @else
                                            {{ $price ? Number::format($price, locale: 'da').' kr.' : '-' }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-primary text-xs space-y-2">
                    <div>S = Standardudstyr</div>
                    <div>Nogle udstyrspakker kan kræve tilvalg af andre pakker eller være uforenelige med bestemt udstyr, farver eller andre tilvalg. Se afhængigheder og eventuelle begrænsninger under den enkelte udstyrsvariant.</div>
                </div>
            </div>
        </section>

        <!-- Trims -->
        @php
            $previousTrim = null;
            $shownEquipmentCodes = [];
        @endphp

        @foreach($car->trims as $trim) 
            <section class="mx-auto max-w-4xl">

                <div class="flex justify-between items-center mb-3">
                    <div class="font-bold text-xl"> 
                        <span class="font-bold"> Udstyr </span>
                        <span class="font-normal">| {{ $trim->name }} </span>
                    </div>
                    <img src="{{ asset('images/logo.png') }}" class="block w-20">
                </div>

                <div>
                    <div class="bg-primary p-2 rounded">
                        <div class="gap-2 grid grid-cols-12 text-xs text-white font-bold">
                            <div class="col-span-4 font-bold">
                                Standardudstyr @if($previousTrim) <span class="font-normal"> - Udstyr fra {{ $previousTrim->name }} og... @endif<span>
                            </div>
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

            </section>

            <section class="mx-auto max-w-4xl mt-8">

                <div>
                    <div class="bg-primary p-2 rounded">
                        <div class="gap-2 grid grid-cols-12 text-xs text-white font-bold">
                            <div class="col-span-4 font-bold">
                                Ekstraudstyr
                            </div>
                             <div class="col-span-8 flex justify-end font-bold">
                                <div class="w-24 text-center">Pris</div>
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
                                        {{  $package->prices->last()?->suggested_retail_price && $package->prices->last()?->suggested_retail_price != 0
                                            ? Number::format($package->prices->last()?->suggested_retail_price, locale: 'da').' kr.'
                                            : '-' 
                                        }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </section>

            @php
                $previousTrim = $trim;
            @endphp

        @endforeach
    </body>
</html>