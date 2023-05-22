<?php

namespace XtendLunar\Addons\RestifyApi\Resources\Customer;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Xtend\Extensions\Lunar\Core\Models\Order;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\OrderPresenter;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        $order = $this->getOrder($request);

        return OrderPresenter::fromData(
            repository: RestifyRepository::resolveWith($order),
            data: $order,
        )->transform(new RestifyRequest(request()->all()));
    }

    protected function getOrder(Request $request): Order
    {
        return $request->user()->orders()->whereId($request->id)->firstOrFail();
    }
}
