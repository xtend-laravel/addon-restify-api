<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Support\Facades\Storage;
use Lunar\Models\Collection;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\Url;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;

class ProductPresenter extends PresenterResource implements Presentable
{
    protected ProductRepository|RestifyRepository $repository;

    public function transform(RestifyRequest $request): array
    {
        return [
            'id' => $this->data['product_id'] ?? $this->data['id'],
            'slug' => $this->getSlug(),
            'name' => $this->repository->resource->translateAttribute('name'),
            'brand' => $this->data['legacy_data']['manufacturer_name'] ?? '--',
            'primary_category_id' => $this->data['primary_category_id'],
            'category_slug' => Url::query()->firstWhere([
                'element_id' => $this->data['primary_category_id'],
                'element_type' => Collection::class,
            ])->slug ?? '--',
            'description' => $this->repository->resource->translateAttribute('description'),
            'status' => $this->data['status'] ?? '--',
            'images' => $this->getter($request, 'product-images'),
            'prices' => $this->getter($request, 'product-prices'),
            'variants' => $request->isShowRequest() ? $this->getter($request, 'product-variants') : [],
            'features' => $this->getter($request, 'product-features'),
            //'related_items' => $this->getter($request, 'product-related-items'),
            'legacy_data' => $this->data['legacy_data'] ?? [],
            'stock' => $this->data['stock'] ?? 0,
            'isNew' => $this->data['created_at']->diffInDays(now()) <= 90,
            'collections' => $this->repository->collections->map(fn (Collection $collection) => [
                'id' => $collection->id,
                'collection_group_id' => $collection->collection_group_id,
                'name' => $collection->translateAttribute('name'),
                'slug' => $collection->urls->first(function (Url $url) {
                    $matchesLocale = $url->language->code === app()->getLocale();

                    return $matchesLocale || $url->language->code === config('app.fallback_locale');
                })?->slug,
            ]),
            'sku' => $this->data['sku'] ?? '--',
            'colors' => $this->getter($request, 'product-variants')['options']['color'] ?? [],
            'seo' => $this->getSeoFields(),
        ];
    }

    protected function getSlug(): string
    {
        return Url::query()->firstWhere([
            'language_id' => Language::query()->firstWhere('code', app()->getLocale())?->id,
            'element_id' => $this->data['product_id'] ?? $this->data['id'],
            'element_type' => Product::class,
        ])->slug ?? '--';
    }

    protected function getSeoFields(): array
    {
        return [
            'title' => $this->repository->resource->translateAttribute('seo_title'),
            'description' => $this->repository->resource->translateAttribute('seo_description'),
            'image' => $this->data['seo_image'] ? Storage::disk('do')->url($this->data['seo_image']) : null,
        ];
    }
}
