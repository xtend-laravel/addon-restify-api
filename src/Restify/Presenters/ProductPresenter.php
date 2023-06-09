<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Lunar\Models\Collection;
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
            'slug' => $this->repository->resource->urls->first(function (Url $url) {
                $matchesLocale = $url->language->code === app()->getLocale();

                return $matchesLocale || $url->language->code === config('app.fallback_locale');
            })?->slug,
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
            'features' => $request->isShowRequest() ? $this->getter($request, 'product-features') : [],
            //'related_items' => $this->getter($request, 'product-related-items'),
            'legacy_data' => $this->data['legacy_data'] ?? [],
            'stock' => $this->data['stock'] ?? 0,
            'isNew' => $this->data['created_at']->diffInDays(now()) <= 90,
        ];
    }
}
