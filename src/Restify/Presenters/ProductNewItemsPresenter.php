<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;

class ProductNewItemsPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        $response = [
            'name' => __('New Items'),
            'image' => null,
        ];

        if (! $request->isIndexRequest()) {
            $response['filters'] = $this->getter($request, 'filter-groups');
        }

        return $response;
    }
}
