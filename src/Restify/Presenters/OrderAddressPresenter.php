<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;

class OrderAddressPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        /** @var \Lunar\Models\Order $order */
        $order = $this->data->order;

        return [
            'id' => $this->data->id,
            'first_name' => $this->data->first_name,
            'last_name' => $this->data->last_name,
            'company_name' => $this->data->company_name,
            'line_one' => $this->data->line_one,
            'line_two' => $this->data->line_two,
            'line_three' => $this->data->line_three,
            'city' => $this->data->city,
            'state' => $this->data->state,
            'postcode' => $this->data->postcode,
            'country' => $this->data->country->name ?? '--',
            'contact_email' => $this->data->contact_email,
            'contact_phone' => $this->data->contact_phone,
            'shipping_option' => $this->data->type === 'shipping' ? $order->shippingLines()->first() : null,
            'tracking_number' => $this->data->type === 'shipping' ? $order->meta?->tracking_number ?? null : null,
        ];
    }
}
