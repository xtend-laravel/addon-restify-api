<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use Lunar\Models\OrderLine;
use XtendLunar\Addons\RestifyApi\Restify\OrderRepository;

class OrderPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        return [
            'id' => $this->data->id,
            'products' => $this->data->lines->transform(function(OrderLine $line) use ($request) {
                $line->purchasable->load('values.option');

                return OrderLinePresenter::fromData(
                    repository: OrderRepository::resolveWith($line),
                    data: $line,
                )->transform($request);
            }),
            'total' => $this->data->total->value,
            // @todo do we format the date on the frontend?
            'created_at' => $this->data->created_at->format('m/d/Y'),
        ];
    }
}
