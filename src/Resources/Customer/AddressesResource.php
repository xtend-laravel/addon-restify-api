<?php

namespace XtendLunar\Addons\RestifyApi\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        return [
            'addresses' => $this->getCustomerAddresses($request),
        ];
    }

    protected function getCustomerAddresses(Request $request): array
    {
        /** @var \Lunar\Models\Customer $customer */
        $customer = $request->user()?->customers()?->first();

        return $customer ? $customer->addresses->toArray() : [];
    }
}
