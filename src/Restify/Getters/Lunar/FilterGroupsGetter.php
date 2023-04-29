<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Services\Search\RepositorySearchService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;
use XtendLunar\Addons\RestifyApi\Resources\CategoryResource;
use XtendLunar\Addons\RestifyApi\Restify\BrandRepository;
use XtendLunar\Addons\RestifyApi\Restify\CategoryRepository;
use XtendLunar\Addons\RestifyApi\Restify\CollectionRepository;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;
use XtendLunar\Addons\RestifyApi\Restify\Repository;
use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Base\BaseModel;
use Lunar\Models\Price;
use Lunar\Models\Brand;
use Xtend\Extensions\Lunar\Core\Models\Collection;

class FilterGroupsGetter extends Getter
{
    public static $uriKey = 'filter-groups';

    public function handle(RestifyRequest $request, BaseModel|Repository $model = null): JsonResponse
    {
        if ($model instanceof CategoryRepository || $model instanceof CollectionRepository) {
            $model = $model->model();
        }

        // brands
        if ($model instanceof BrandRepository) {
            $model = $model->model();
            return data([
                'groups' => [
                    'categories' => CategoryResource::make(Collection::query()->first()),
                    'brands'     => $this->getBrands($model),
                    'price'      => $this->getPriceFilter($model, $request),
                    'options'    => $this->getOptions($model, $request),
                ],
            ]);
        }

        // collection
        return data([
            'groups' => [
                'categories' => CategoryResource::make($model),
                'brands'     => $this->getBrands($model),
                'price'      => $this->getPriceFilter($model, $request),
                'options'    => $this->getOptions($model, $request),
            ],
        ]);
    }

    protected function getCategories(BaseModel $model): array
    {
        return CategoryResource::make($model)->resolve();
    }

    protected function getBrands(BaseModel $model): array
    {
        $productQuery = $model->products()->where('status', 'published');

        return Brand::query()->find(
            id: $productQuery->pluck('brand_id')->unique(),
            columns: ['id', 'name'],
        )->map(fn(Brand $brand) => [
            'id'    => $brand->id,
            'name'  => $brand->name,
            'count' => $productQuery->where('brand_id', $brand->id)->count(),
        ])->toArray();
    }

    protected function getPriceFilter(BaseModel $model, RestifyRequest $request): array
    {
        $productQuery = RepositorySearchService::make()->search($request, app()->make(ProductRepository::class));

        $priceRange = Price::query()
            ->selectRaw('min(price) as min, max(price) as max')
            ->whereIntegerInRaw('id', $productQuery->pluck('price_default_id')->filter())
            ->first()
            ->toArray();

        ['min' => $min, 'max' => $max] = $priceRange;

        if ($min === null || $max === null) {
            return ['min ' => Price::min('price'), 'max' => Price::max('price')];
        }

        return $priceRange;
    }

    protected function getOptions(BaseModel $model, RestifyRequest $request): array
    {
        $newRequest = match ($request->currentGroup) {
            'colors' => $request->except(['availableColorIds']),
            'sizes'  => $request->except(['availableSizeIds']),
            default  => $request->all(),
        };

        $request = new RestifyRequest($newRequest);
        $productQuery = RepositorySearchService::make()->search($request, app()->make(ProductRepository::class));

        $sizeOption = ProductOption::where('handle', 'size')->first();
        $colorOption = ProductOption::where('handle', 'color')->first();
        $productIds = $productQuery->pluck('id');
        $variants = ProductVariant::query()->select('id')->where('stock', '>', 0)->whereIntegerInRaw('product_id', $productIds)->get();
        $variantIds = $variants->pluck('id');

        return [
            'sizes'  => $this->getAttributeOptionsValues($sizeOption, $variantIds)->toArray(),
            'colors' => $this->getAttributeOptionsValues($colorOption, $variantIds)->toArray(),
        ];
    }

    protected function getAttributeOptionsValues(ProductOption $productOption, \Illuminate\Support\Collection $variantIds): \Illuminate\Support\Collection
    {
        return $productOption
            ->values()
            ->whereHas('variants', fn($query) => $query->whereIntegerInRaw('variant_id', $variantIds))
            ->get()
            ->map(fn(ProductOptionValue $value) => [
                'id' => $value->id,
                'name' => $value->name,
                'position' => $value->position,
            ]);
    }
}
