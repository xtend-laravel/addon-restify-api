<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;

class BrandPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        $response = [
            'id' => $this->data['id'],
            'name' => $this->data['name'],
            'image' => $this->repository->model()->getFirstMediaUrl('brands', 'small'),
        ];

        if (! $request->isIndexRequest()) {
            $response['filters'] = $this->getter($request, 'filter-groups');
        }

        return $response;
    }
}
