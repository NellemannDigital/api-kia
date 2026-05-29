{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<listings>
    <title>Kia cars feed</title>
    <updated>{{ $generatedAt->toAtomString() }}</updated>
    <link>{{ route('feeds.cars') }}</link>

@foreach($listings as $listing)
    <listing>
        <id>{{ $listing['id'] }}</id>
        <vehicle_id>{{ $listing['vehicle_id'] }}</vehicle_id>
        <title>{{ $listing['title'] }}</title>

        @if(filled($listing['url']))
            <url>{{ $listing['url'] }}</url>
            <link>{{ $listing['link'] }}</link>
        @endif

        <make>{{ $listing['make'] }}</make>

        @if(filled($listing['model']))
            <model>{{ $listing['model'] }}</model>
        @endif

        @if(filled($listing['trim']))
            <trim>{{ $listing['trim'] }}</trim>
        @endif

        @if(filled($listing['year']))
            <year>{{ $listing['year'] }}</year>
        @endif

        <mileage>
            <value>{{ $listing['mileage']['value'] }}</value>
            <unit>{{ $listing['mileage']['unit'] }}</unit>
        </mileage>

        @if(filled($listing['image_url']))
            <image>
                <url>{{ $listing['image_url'] }}</url>
                <tag>{{ $listing['title'] }}</tag>
            </image>
            <image_link>{{ $listing['image_link'] }}</image_link>
        @endif

        @if(filled($listing['transmission']))
            <transmission>{{ $listing['transmission'] }}</transmission>
        @endif

        @if(filled($listing['body_style']))
            <body_style>{{ $listing['body_style'] }}</body_style>
        @endif

        @if(filled($listing['drivetrain']))
            <drivetrain>{{ $listing['drivetrain'] }}</drivetrain>
        @endif

        @if(filled($listing['vin']))
            <vin>{{ $listing['vin'] }}</vin>
        @endif

        @if(filled($listing['price']))
            <price>{{ $listing['price'] }}</price>
            <currency>{{ $listing['currency'] }}</currency>
        @endif

        <state_of_vehicle>{{ $listing['state_of_vehicle'] }}</state_of_vehicle>

        @if(filled($listing['fuel_type']))
            <fuel_type>{{ $listing['fuel_type'] }}</fuel_type>
        @endif

        <condition>{{ $listing['condition'] }}</condition>
        <availability>{{ $listing['availability'] }}</availability>
        <vehicle_type>{{ $listing['vehicle_type'] }}</vehicle_type>
        <brand>{{ $listing['brand'] }}</brand>
    </listing>
@endforeach
</listings>
