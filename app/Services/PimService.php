<?php

namespace App\Services;

use App\Data\CarData;
use App\Data\ConfigurationData;
use App\Data\AccessoryData;
use App\Data\TrimData;
use App\Requests\ProductRequest;
use App\Requests\VariantRequest;
use App\Requests\AssetRequest;
use App\Mappers\CarMapper;
use App\Mappers\ConfigurationMapper;
use App\Mappers\AccessoryMapper;
use App\Mappers\TrimMapper;
use App\Mappers\AssetMapper;
use Illuminate\Support\Facades\Log;
use Throwable;

class PimService
{
    public function __construct(
        protected ProductRequest $productRequest,
        protected VariantRequest $variantRequest,
        protected AssetRequest $assetRequest,
        protected CarMapper $carMapper,
        protected ConfigurationMapper $configurationMapper,
        protected AccessoryMapper $accessoryMapper,
        protected TrimMapper $trimMapper,
        protected AssetMapper $assetMapper,
    ) {}

    public function getProductStructureId(int $productId)
    {
        return $this->productRequest->getProductResponse($productId)->get('ProductStructureUid');
    }

    public function getCar(int $productId): ?CarData
    {
        return $this->safeCall(fn() => $this->buildCarData($productId), $productId);
    }

    protected function buildCarData(int $productId): CarData
    {
        $productData = $this->productRequest->getProductResponse($productId);
        $productAttributesData = $this->productRequest->getProductAttributesResponse($productId);

        return $this->carMapper->map(
            $productData,
            $productAttributesData,
            fn ($id) => $this->getAsset($id)
        );
    }

    public function getTrim(int $variantId): ?TrimData
    {
        return $this->safeCall(fn() => $this->buildTrimData($variantId), $variantId);
    }

    protected function buildTrimData(int $variantId): TrimData
    {
        $variantData = $this->variantRequest->getVariantResponse($variantId);
        $variantAttributesData = $this->variantRequest->getVariantAttributesResponse($variantId);
        $variantAttributesReferencesData = $this->variantRequest->getVariantAttributesReferencesResponse($variantId);

        return $this->trimMapper->map(
            $variantData,
            $variantAttributesData,
            $variantAttributesReferencesData,
            fn ($id) => $this->getAsset($id)
        );
    }

    public function getConfiguration(int $productId): ?ConfigurationData
    {
        return $this->safeCall(fn() => $this->buildConfigurationData($productId), $productId);
    }

    protected function buildConfigurationData(int $productId): ?ConfigurationData
    {
        $productData = $this->productRequest->getProductResponse($productId);
        $productAttributesData = $this->productRequest->getProductAttributesResponse($productId);

        return $this->configurationMapper->map(
            $productData,
            $productAttributesData,
            fn ($id) => $this->getAsset($id)
        );
    }

    public function getAccessory(int $productId): ?AccessoryData
    {
        return $this->safeCall(fn() => $this->buildAccessoryData($productId), $productId);
    }

    protected function buildAccessoryData(int $productId): ?AccessoryData
    {
        $productData = $this->productRequest->getProductResponse($productId);
        $productAttributesData = $this->productRequest->getProductAttributesResponse($productId);
        $productAttributesReferencesData = $this->productRequest->getProductAttributesReferencesResponse($productId);


        return $this->accessoryMapper->map(
            $productData,
            $productAttributesData,
            $productAttributesReferencesData,
            fn ($id) => $this->getAsset($id)
        );
    }

    public function getAsset(int|string|null $assetId)
    {
        if (!is_numeric($assetId)) {
            return null;
        }

        $assetData = $this->assetRequest->getAssetResponse($assetId);
        return $this->assetMapper->map($assetData);
    }

    protected function safeCall(callable $callback)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            Log::error('Failed to fetch data from PIM', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            report($e);

            return null;
        }
    }
}
