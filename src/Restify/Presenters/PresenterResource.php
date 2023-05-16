<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Str;

abstract class PresenterResource
{
    protected mixed $queryParams = [];

    public function __construct(
        protected RestifyRepository $repository,
        protected mixed $data = []
    ) {
        if (request()->has('params') && Str::isJson(request()->params)) {
            $this->queryParams = Json::decode(request()->params);
        }
    }

    public static function fromData(RestifyRepository $repository, mixed $data): static
    {
        return new static($repository, $data);
    }

    public function getter(RestifyRequest $request, string $getterUri): array
    {
        return $this->repository
            ->resolveGetters($request)
            ->filter(fn (Getter $getter) => $getter->uriKey() === $getterUri)
            ->first()
            ->handle($request, $this->repository)
            ->getData(true);
    }
}
