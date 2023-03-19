<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Contracts;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

interface Presentable
{
    public function transform(RestifyRequest $request): array;
}
