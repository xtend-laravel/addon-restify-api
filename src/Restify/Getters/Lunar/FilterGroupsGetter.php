<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Services\Search\RepositorySearchService;
use Illuminate\Database\Query\Builder;
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
                    'price'      => $this->getPriceFilter($model),
                    'options'    => $this->getOptions($model),
                ],
            ]);
        }

        // collection
        return data([
            'groups' => [
                'categories' => CategoryResource::make($model),
                'brands'     => $this->getBrands($model),
                'price'      => $this->getPriceFilter($model, $request),
                'options'    => $this->getOptions($model),
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

    protected function getOptions(BaseModel $model): array
    {
        $productQuery = $model->products()->where('status', 'published');

        $sizeOption = ProductOption::where('handle', 'size')->first();
        $colorOption = ProductOption::where('handle', 'color')->first();

        $productIds = $productQuery->pluck($productQuery->qualifyColumn('id'));
        $variantIds = ProductVariant::whereIn('product_id', $productIds)->pluck('id');

        $sizeOptionValues = $sizeOption->values()->whereHas('variants', function (\Illuminate\Contracts\Database\Query\Builder $query) use ($variantIds) {
            $query->whereIn($query->qualifyColumn('id'), $variantIds);
        })->get();

        $colorOptionValues = $colorOption->values()->whereHas('variants', function (\Illuminate\Contracts\Database\Query\Builder $query) use ($variantIds) {
            $query->whereIn($query->qualifyColumn('id'), $variantIds);
        })->get();

        return [
            'sizes'  => $sizeOptionValues->toArray(),
            'colors' => $colorOptionValues->toArray(),
        ];
    }
}
