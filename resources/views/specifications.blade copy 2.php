<div class="overflow-x-auto w-full">

    <table class="min-w-full border-collapse text-sm">

        {{-- HEADER --}}
        <thead class="bg-gray-100 sticky top-0 z-10">
            <tr>

                <th class="text-left p-3 border-b">
                    Specifikation
                </th>

                @foreach($data['columns'] as $group)
                    <th class="text-left p-3 border-b whitespace-nowrap align-top">

                        @foreach($group['columns'] as $col)
                            <div class="font-semibold">
                                {{ $col['trim']->name }}
                            </div>

                            <div class="text-xs text-gray-500 mb-1">
                                {{ $col['powertrain']->engine->name }}
                            </div>
                        @endforeach

                    </th>
                @endforeach

            </tr>
        </thead>

        {{-- BODY --}}
        <tbody>

            @foreach($data['sections'] as $section)

                {{-- SECTION TITLE --}}
                <tr class="bg-gray-50">
                    <td colspan="{{ count($data['columns']) + 1 }}"
                        class="p-3 font-bold border-y">
                        {{ $section['title'] }}
                    </td>
                </tr>

                {{-- ROWS --}}
                @foreach($section['rows'] as $row)
                    <tr class="border-b hover:bg-gray-50">

                        <td class="p-3 font-medium whitespace-nowrap">
                            {{ $row['label'] }}
                        </td>

                        {{-- 👇 KEY FIX: use display_index per group --}}
                        @foreach($data['columns'] as $group)

                            @php
                                $i = $group['display_index'];
                            @endphp

                            <td class="p-3 whitespace-nowrap">
                                {{ $row['values'][$i] ?? '-' }}
                            </td>

                        @endforeach

                    </tr>
                @endforeach

            @endforeach

        </tbody>

    </table>

</div>