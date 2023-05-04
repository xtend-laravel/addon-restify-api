<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Services\Search\RepositorySearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;
use XtendLunar\Addons\RestifyApi\Resources\CategoryResource;
use XtendLunar\Addons\RestifyApi\Restify\BrandRepository;
use XtendLunar\Addons\RestifyApi\Restify\CategoryRepository;
use XtendLunar\Addons\RestifyApi\Restify\CollectionRepository;
use XtendLunar\Addons\RestifyApi\Restify\ProductNewItemsRepository;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;
use XtendLunar\Addons\RestifyApi\Restify\ProductSaleItemsRepository;
use XtendLunar\Addons\RestifyApi\Restify\Repository;
use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Base\BaseModel;
use Lunar\Models\Price;
use Lunar\Models\Brand;

class FilterGroupsGetter extends Getter
{
    protected BaseModel|Repository $model;

    protected RestifyRequest $request;

    protected Builder $productQuery;

    public static $uriKey = 'filter-groups';

    public function handle(RestifyRequest $request, BaseModel|Repository $model = null): JsonResponse
    {
        $this->model = $model;
        $request = $request->merge([
            'repositoryUri' => $this->model->uriKey(),
        ]);

        if ($model instanceof BrandRepository) {
            return data([
                'groups' => [
                    'categories' => $this->getCategories($request),
                    'brands'     => $this->getBrands($request),
                    'price'      => $this->getPriceFilter($request),
                    'options'    => $this->getOptions($request),
                ],
            ]);
        }

        if ($model instanceof ProductNewItemsRepository || $model instanceof ProductSaleItemsRepository) {
            return data([
               'groups' => [
                   //'categories' => $this->getCategories($request),
                   'brands'     => $this->getBrands($request),
                   'price'      => $this->getPriceFilter($request),
                   'options'    => $this->getOptions($request),
               ],
            ]);
        }

        return data([
            'groups' => [
                'categories' => CategoryResource::make($this->model->model(), $request),
                'brands'     => $this->getBrands($request),
                'price'      => $this->getPriceFilter($request),
                'options'    => $this->getOptions($request),
            ],
        ]);
    }

    protected function getCategories(RestifyRequest $request): Collection
    {
        $this->interceptRequest($request, 'categories');
        $categories = $this->productQuery->pluck('primary_category_id')->unique();

        return $categories->map(fn($categoryId) => CategoryResource::make(
            \Lunar\Models\Collection::find($categoryId),
            $request,
        ))->values();
    }

    protected function getBrands(RestifyRequest $request): array
    {
        $this->interceptRequest($request, 'brands');

        $brandCounts = Product::query()
            ->selectRaw('count(*) as brand_count, brand_id')
            ->whereNotNull('brand_id')
            ->whereIntegerInRaw('id', $this->productQuery->pluck('id'))
            ->where('status', 'published')
            ->where('stock', '>', 0)
            ->groupBy('brand_id')
            ->get()
            ->keyBy('brand_id');

        return Brand::query()->find(
            id: $this->productQuery->pluck('brand_id')->unique(),
            columns: ['id', 'name'],
        )->filter(fn(Brand $brand) => $brand->products->count() > 1)->map(fn(Brand $brand) => [
            'id'    => $brand->id,
            'name'  => $brand->name,
            'count' => $brandCounts->get($brand->id)?->brand_count ?? 0,
        ])->toArray();
    }

    /**
     * @todo Optimise query and get correct price range to consider discounted products
     */
    protected function getPriceFilter(RestifyRequest $request): array
    {
        $this->interceptRequest($request, 'price');

        $priceRange = Price::query()
            ->selectRaw('min(price) as min, max(price) as max')
            ->whereIntegerInRaw('id', $this->productQuery->pluck('price_default_id')->filter())
            ->first()
            ->toArray();

        ['min' => $min, 'max' => $max] = $priceRange;

        if ($min === null || $max === null) {
            return ['min ' => Price::min('price'), 'max' => Price::max('price')];
        }

        return $priceRange;
    }

    protected function getOptions(RestifyRequest $request): array
    {
        $this->interceptRequest($request, 'options');

        $sizeOption = ProductOption::where('handle', 'size')->first();
        $colorOption = ProductOption::where('handle', 'color')->first();
        $productIds = $this->productQuery->pluck('id');
        $variants = ProductVariant::query()->select('id')->where('stock', '>', 0)->whereIntegerInRaw('product_id', $productIds)->get();
        $variantIds = $variants->pluck('id');

        return [
            'sizes'  => $this->getAttributeOptionsValues($sizeOption, $variantIds)->toArray(),
            'colors' => $this->getAttributeOptionsValues($colorOption, $variantIds)->toArray(),
        ];
    }

    protected function getAttributeOptionsValues(ProductOption $productOption, Collection $variantIds): Collection
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

    protected function interceptRequest(RestifyRequest $request, string $for = null): void
    {
        $newRequest = match ($request->currentGroup) {
            'brands' => $request->except(['brand_id']),
            'colors' => $request->except(['availableColorIds']),
            'sizes'  => $request->except(['availableSizeIds']),
            default  => $request->all(),
        };

        if ($for === 'options' && in_array($request->currentGroup, ['colors', 'sizes'])) {
            $for = $request->currentGroup;
        }

        $this->request = $request->currentGroup === $for
            ? new RestifyRequest($newRequest)
            : $request;

        $this->productQuery = RepositorySearchService::make()->search($this->request, app()->make(ProductRepository::class));


        // @todo: Replace this when we have a better way to handle this temp work around to filter initial products by brand
        if ($this->model instanceof BrandRepository) {
            $this->productQuery->where('brand_id', $this->model->model()->id);
        }

        $this->productQuery->where('status', 'published');
        $this->productQuery->where('stock', '>', 0);
    }
}
