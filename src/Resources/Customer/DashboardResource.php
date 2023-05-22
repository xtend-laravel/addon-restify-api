<?php

namespace XtendLunar\Addons\RestifyApi\Resources\Customer;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Xtend\Extensions\Lunar\Core\Models\Order;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\OrderPresenter;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        /** @var Order[] $orders */
        $orders = $request->user()->orders()->get();

        return [
            'stats' => [
                'orders' => $orders->count(),
                'total_spent' => $this->getTotalSpent($orders),
                'points_earned' => $this->getPointsEarned($orders),
            ],
            'latest_order' => $this->getLatestOrder($orders->first()),
        ];
    }

    protected function getTotalSpent($orders)
    {
        // @todo do we use decimal here, or the value and then format it on the frontend?
        return $orders->sum(fn(Order $order) => $order->total->decimal);
    }

    // @todo Addons will be able to append to this array so this will be moved later
    // implement some logic?
    protected function getPointsEarned($orders)
    {
        return 0;
    }

    protected function getLatestOrder(Order $order)
    {
        return OrderPresenter::fromData(
            repository: RestifyRepository::resolveWith($order),
            data: $order,
        )->transform(new RestifyRequest(request()->all()));
    }
}
