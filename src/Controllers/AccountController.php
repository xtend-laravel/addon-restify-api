<?php

namespace XtendLunar\Addons\RestifyApi\Controllers;

use Binaryk\LaravelRestify\Http\Controllers\RestController;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class AccountController
 *
 * @todo Each section of the account separate invokable controllers - currently single controller for all sections due to time constraints
 */
class AccountController extends RestController
{
    public function __invoke(RestifyRequest $request, string $section = null)
    {
        $section = Str::camel($section);
        if (method_exists($this, $section)) {
            return $this->{$section}();
        }

        if (filled($section)) {
            return $this->response([
                'message' => __('Account section :section not found', ['section' => $section]),
            ])->missing();
        }

        return $this->dashboard();
    }

    public function dashboard(): JsonResponse
    {
        return $this->response([
            'message' => 'Dashboard',
        ]);
    }

    public function orders(): JsonResponse
    {
        return $this->response([
            'message' => 'Orders',
        ]);
    }

    public function identity(): JsonResponse
    {
        return $this->response([
            'message' => 'Identity',
        ]);
    }

    public function addresses(): JsonResponse
    {
        return $this->response([
            'message' => 'Addresses',
        ]);
    }

    public function creditNotes(): JsonResponse
    {
        return $this->response([
            'message' => 'Credit Notes',
        ]);
    }

    public function discount(): JsonResponse
    {
        return $this->response([
            'message' => 'Discount',
        ]);
    }

    public function rma(): JsonResponse
    {
        return $this->response([
            'message' => 'RMA',
        ]);
    }

    public function wishlist(): JsonResponse
    {
        return $this->response([
            'message' => 'Wishlist',
        ]);
    }
}
