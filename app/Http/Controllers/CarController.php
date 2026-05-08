<?php

namespace App\Http\Controllers;

use App\Jobs\SyncCarJob;
use App\Jobs\SyncTrimJob;
use App\Jobs\GeneratePdfJob;
use App\Models\Car;
use App\Requests\ProductRequest;
use App\Services\ComplianceTextService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Inertia\Inertia;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use App\Services\PdfService;
use function Spatie\LaravelPdf\Support\pdf;

class CarController extends Controller
{
    public function __construct(
        private PdfService $pdfService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('cars/index', [
            'cars' => Car::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // return Car::where('web_id', $id)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function sync($id)
    {
        $jobs = [
            new SyncCarJob($id),
        ];

        $productVariantIds = (new ProductRequest)->getProductVariantsIdsByProductId($id);

        foreach ($productVariantIds as $variantId) {
            $jobs[] = new SyncTrimJob($variantId);
        }

        $jobs[] = new GeneratePdfJob($id);

        $batch = Bus::batch($jobs)
            ->onQueue('pim')
            ->dispatch();

        return redirect()->back()->with([
            'batch_id' => $batch->id,
            'message' => 'Sync started',
        ]);
    }

    public function prices($id)
    {
        $car = $this->pdfService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->pdfService
            ->pdf($car, 'prices');
    }

    public function acessories($id)
    {
        $car = $this->pdfService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->pdfService
            ->pdf($car, 'accessories');
    }

    public function specifications($id)
    {
        $car = $this->pdfService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->pdfService
            ->pdf($car, 'specifications', true);
    }

    public function pricesView($id)
    {
        $car = $this->pdfService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->pdfService
            ->view($car, 'prices');
    }

    public function accessoriesView($id)
    {
        $car = $this->pdfService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->pdfService
            ->view($car, 'accessories');
    }

    public function specificationsView($id)
    {
        $car = $this->pdfService->loadCar($id);

        if (! $car) {
            abort(404, 'Car not found');
        }

        return $this->pdfService
            ->view($car, 'specifications');
    }
}
