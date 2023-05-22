<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Lunar\Models\OrderLine;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use XtendLunar\Addons\RestifyApi\Restify\OrderRepository;

class OrderPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        return [
            'id' => $this->data->id,
            'products' => $this->data->lines->transform(function (OrderLine $line) use ($request) {
                $line->purchasable->load('values.option');

                return OrderLinePresenter::fromData(
                    repository: OrderRepository::resolveWith($line),
                    data: $line,
                )->transform($request);
            }),
            'total' => $this->data->total->value,
            'status' => $this->data->status,
            // @todo do we format the date on the frontend?
            'created_at' => $this->data->created_at->format('m/d/Y'),
            'addresses' => $this->getAddresses($request),
        ];
    }

    protected function getAddresses(RestifyRequest $request)
    {
        return $this->data->addresses->keyBy('type')
            ->mapWithKeys(function ($address, $type) use ($request) {
                return [
                    $type => OrderAddressPresenter::fromData(
                        repository: RestifyRepository::resolveWith($address),
                        data: $address,
                    )->transform($request),
                ];
            });
    }
}
