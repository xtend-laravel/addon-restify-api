<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Concerns;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;

/**
 * Trait InteractsWithPresenter
 *
 * @mixin Repository
 */
trait InteractsWithPresenter
{
    protected static string $presenter;

    protected function getPresenter(array $data): Presentable
    {
        return app(static::$presenter, [
            'repository' => $this,
            'data' => $data,
        ]);
    }

    public function resolveIndexAttributes($request): array
    {
        return $this->present(
            request: $request,
            data: parent::resolveIndexAttributes($request),
        );
    }

    public function resolveShowAttributes(RestifyRequest $request): array
    {
        return $this->present(
            request: $request,
            data: parent::resolveShowAttributes($request),
        );
    }

    protected function present(RestifyRequest $request, array $data): array
    {
        if (! $data) {
            return $data;
        }

        return (static::$presenter ?? null)
            ? $this->getPresenter($data)->transform($request)
            : $data;
    }
}
