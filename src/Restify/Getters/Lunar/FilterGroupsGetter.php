<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use XtendLunar\Addons\RestifyApi\Resources\CategoryResource;
use XtendLunar\Addons\RestifyApi\Restify\BrandRepository;
use XtendLunar\Addons\RestifyApi\Restify\CategoryRepository;
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
        if ($model instanceof CategoryRepository) {
            $model = $model->model();
        }

        if ($model instanceof BrandRepository) {
            $model = $model->model();
            return data([
                'groups' => [
                    'categories' => CategoryResource::make(Collection::find(2)),
                    'brands' => $this->getBrands($model),
                    'price' => $this->getPriceFilter($model),
                    'options' => $this->getOptions($model),
                ],
            ]);
        }

        return data([
            'groups' => [
                'categories' => CategoryResource::make($model),
                'brands' => $this->getBrands($model),
                'price' => $this->getPriceFilter($model),
                'options' => $this->getOptions($model),
            ],
        ]);
    }

    protected function getBrands(BaseModel $model): array
    {
        $productQuery = $model->products()->where('status', 'published');
        return Brand::query()->find(
            id: $productQuery->pluck('brand_id')->unique(),
            columns: ['id', 'name'],
        )->map(fn (Brand $brand) => [
            'id' => $brand->id,
            'name' => $brand->name,
            'count' => $productQuery->where('brand_id', $brand->id)->count(),
        ])->toArray();
    }

    protected function getPriceFilter(BaseModel $model): array
    {
        $productQuery = $model->products()->where('status', 'published');
        return Price::query()
            ->selectRaw('min(price) as min, max(price) as max')
            ->whereIntegerInRaw('id', $productQuery->pluck('price_default_id')->filter())
            ->first()
            ->toArray();
    }

    protected function getOptions(BaseModel $model): array
    {
        $productQuery = $model->products()->where('status', 'published');
        return [
            'sizes' => [],
            'colors' => [],
        ];
    }
}
