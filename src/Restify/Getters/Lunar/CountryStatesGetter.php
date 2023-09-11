<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Country;
use Symfony\Component\HttpFoundation\Response;

class CountryStatesGetter extends Getter
{
    public static $uriKey = 'country-states';

    public function handle(RestifyRequest $request, ?Model $model = null): JsonResponse|Response
    {
        $countries = Country::all();

        return response()->json([
            'countries' => $countries,
            'countryStates' => $countries->map(function ($country) {
                return [
                    'country' => $country,
                    'states' => $country->states,
                ];
            }),
        ]);
    }
}
