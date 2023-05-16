<?php

namespace XtendLunar\Addons\RestifyApi\Resources\Account;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\CartLinePresenter;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        /** @var \Lunar\Models\Cart $cart */
        $cart = $request->user()->carts()->whereHas('lines')->latest()->first();

        return [
            'stats' => [
                'orders' => '0',
                'total_spent' => '0',

                // @todo Addons will be able to append to this array so this will be moved later
                'points_earned' => '0',
            ],
            'latest_order' => $this->getLatestOrder($cart),
        ];
    }

    protected function getLatestOrder(Cart $cart): Collection
    {
        return collect([
            'products' => $cart->lines->transform(function (CartLine $line) use ($cart) {
                $line->purchasable->load('values.option');

                return CartLinePresenter::fromData(
                    repository: RestifyRepository::resolveWith($cart),
                    data: $line,
                )->transform(new RestifyRequest(request()->all()));
            }),
        ]);
    }
}
