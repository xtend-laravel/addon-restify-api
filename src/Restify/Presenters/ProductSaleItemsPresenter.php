<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class ProductSaleItemsPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        $response = [
            'name' => __('Sale Items'),
            'image' => null,
        ];

        if (!$request->isIndexRequest()) {
            $response['filters'] = $this->getter($request, 'filter-groups');
        }

        return $response;
    }
}


