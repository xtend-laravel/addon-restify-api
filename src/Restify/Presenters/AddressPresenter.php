<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Lunar\Models\Country;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;

class AddressPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        $this->data['country_iso'] = Country::find($this->data['country_id'])->iso2;

        return $this->data;
    }
}
