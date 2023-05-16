<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;

class UserPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        /** @var \Lunar\Models\Customer $customer */
        $customer = $this->repository->resource->customers->first();

        return [
            'id' => $this->data['id'],
            'language_id' => $customer->language_id,
            'email' => $this->data['email'],
            'title' => $customer->title,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'company_name' => $customer->company_name,
            'meta' => [
                'birth_data' => $customer->meta['birth_data'] ?? null,
                'newsletter' => $customer->meta['newsletter'] ?? false,
                'terms' => $customer->meta['terms'] ?? false,
            ],
        ];
    }
}
