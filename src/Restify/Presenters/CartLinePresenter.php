<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use XtendLunar\Addons\RestifyApi\Restify\ProductRepository;

class CartLinePresenter extends PresenterResource implements Presentable
{
    protected ProductRepository|RestifyRepository $repository;

    public function transform(RestifyRequest $request): array
    {
        return [
            'id' => $this->data->id,
            'product' => ProductPresenter::fromData(
                repository: ProductRepository::resolveWith($this->data->purchasable->product),
                data: $this->data->purchasable->product,
            )->transform($request),
            'purchasable' => [
                ...$this->data->purchasable->toArray(),
                'images' => ['thumbnail' => $this->data->purchasable->images->first()?->getUrl('medium')],
            ],
            'quantity' => $this->data->quantity,
            'total' => $this->data->total?->value,
        ];
    }
}
