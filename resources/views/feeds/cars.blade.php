{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>Kia cars feed</title>
        <link>{{ route('feeds.cars') }}</link>
        <description>Kia cars feed</description>
        <lastBuildDate>{{ $generatedAt->toRssString() }}</lastBuildDate>

@foreach($listings as $listing)
        <item>
            <g:id>{{ $listing['id'] }}</g:id>
            <g:vehicle_id>{{ $listing['vehicle_id'] }}</g:vehicle_id>
            <g:title>{{ $listing['title'] }}</g:title>
            <g:description>{{ $listing['description'] }}</g:description>

            @if(filled($listing['url']))
                <g:link>{{ $listing['link'] }}</g:link>
                <link>{{ $listing['link'] }}</link>
            @endif

            <g:make>{{ $listing['make'] }}</g:make>
            <g:brand>{{ $listing['brand'] }}</g:brand>

            @if(filled($listing['model']))
                <g:model>{{ $listing['model'] }}</g:model>
            @endif

            @if(filled($listing['trim']))
                <g:trim>{{ $listing['trim'] }}</g:trim>
            @endif

            @if(filled($listing['year']))
                <g:year>{{ $listing['year'] }}</g:year>
            @endif

            <g:mileage>
                <g:value>{{ $listing['mileage']['value'] }}</g:value>
                <g:unit>{{ $listing['mileage']['unit'] }}</g:unit>
            </g:mileage>

            @if(filled($listing['image_url']))
                <g:image_link>{{ $listing['image_link'] }}</g:image_link>
            @endif

            @if(filled($listing['transmission']))
                <g:transmission>{{ $listing['transmission'] }}</g:transmission>
            @endif

            @if(filled($listing['drivetrain']))
                <g:drivetrain>{{ $listing['drivetrain'] }}</g:drivetrain>
            @endif

            @if(filled($listing['vin']))
                <g:vin>{{ $listing['vin'] }}</g:vin>
            @endif

            @if(filled($listing['price']))
                <g:price>{{ $listing['price'] }}</g:price>
            @endif

            @if(filled($listing['exterior_color']))
                <g:exterior_color>{{ $listing['exterior_color'] }}</g:exterior_color>
            @endif

            @if(filled($listing['interior_color']))
                <g:interior_color>{{ $listing['interior_color'] }}</g:interior_color>
            @endif

            <g:state_of_vehicle>{{ $listing['state_of_vehicle'] }}</g:state_of_vehicle>

            @if(filled($listing['fuel_type']))
                <g:fuel_type>{{ $listing['fuel_type'] }}</g:fuel_type>
            @endif

            <g:condition>{{ $listing['condition'] }}</g:condition>
            <g:availability>{{ $listing['availability'] }}</g:availability>
            <g:vehicle_type>{{ $listing['vehicle_type'] }}</g:vehicle_type>
        </item>
@endforeach
    </channel>
</rss>
