<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Address;
use Lunar\Models\Country;
use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithDefaultFields;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar\CountryStatesGetter;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\AddressPresenter;

class AddressRepository extends Repository
{
    use InteractsWithDefaultFields;

    public static string $model = Address::class;

    public static string $presenter = AddressPresenter::class;

    public function destroy(RestifyRequest $request, $repositoryId)
    {
        $status = DB::transaction(function () {
            return $this->resource->forceDelete();
        });

        static::deleted($status, $request);

        return ok(code: 204);
    }

    public function update(RestifyRequest $request, $repositoryId)
    {
        $country = Country::firstWhere('name', $request->input('country'));

        $this->resource->update(
            $request->validated()+
            ['country_id' => $country?->id]
        );

        return ok();
    }

    public function getters(RestifyRequest $request): array
    {
        return [
            CountryStatesGetter::make()->onlyOnIndex(),
        ];
    }
}
