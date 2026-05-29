<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\ViewModels\MetaCarsFeed;
use Illuminate\Http\Response;

class FeedsController extends Controller
{
    public function cars(): Response
    {
        $cars = Car::query()
            ->addChannels(['web_channel', 'price_channel'])
            ->where('variant->b2b', false)
            ->with([
                'trims.colors',
                'trims.powertrains.configuration',
                'trims.powertrains.prices',
            ])
            ->orderBy('name')
            ->get();

        return response()
            ->view('feeds.cars', [
                'listings' => (new MetaCarsFeed(
                    $cars,
                    config('services.meta_feed.currency', 'DKK')
                ))->build(),
                'generatedAt' => now(),
            ])
            ->header('Content-Type', 'application/xml');
    }
}
