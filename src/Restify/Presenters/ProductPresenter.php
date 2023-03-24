<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Lunar\Models\Collection;
use Lunar\Models\Url;

class ProductPresenter extends PresenterResource implements Presentable
{
    protected ProductRepository|RestifyRepository $repository;

    public function transform(RestifyRequest $request): array
    {
        return [
            'id' => $this->data['product_id'] ?? $this->data['id'],
            'name' => $this->data['attribute_data']['name'],
            'brand' => $this->data['legacy_data']['manufacturer_name'] ?? '--',
            'primary_category_id' => $this->data['primary_category_id'],
            'category_slug' => Url::query()->firstWhere([
                'element_id' => $this->data['primary_category_id'],
                'element_type' => Collection::class,
            ])->slug ?? '--',
            'description' => $this->data['attribute_data']['description'],
            'status' => $this->data['status'] ?? '--',
            'images' => $this->getter($request, 'product-images'),
            'prices' => $this->getter($request, 'product-prices'),
            'legacy_data' => $this->data['legacy_data'] ?? [],
        ];
    }
}


