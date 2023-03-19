<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;

abstract class PresenterResource
{
    public function __construct(
        protected RestifyRepository $repository,
        protected mixed $data = []
    ) {}

    public static function fromData(RestifyRepository $repository, mixed $data): static
    {
        return new static($repository, $data);
    }

    protected function getter(RestifyRequest $request, string $getterUri): array
    {
        return $this->repository
            ->resolveGetters($request)
            ->filter(fn(Getter $getter) => $getter->uriKey() === $getterUri)
            ->first()
            ->handle($request, $this->repository)
            ->getData(true);
    }
}
