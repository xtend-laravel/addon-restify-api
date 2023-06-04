<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Cart;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\OrderPresenter;

class PlaceOrderAction extends Action
{
    public function handle(ActionRequest $request, Cart $cart): JsonResponse
    {
        $order = $cart->createOrder();

        return data(OrderPresenter::fromData(
                repository: RestifyRepository::resolveWith($order),
                data: $order,
            )->transform($request)
        );
    }
}
