<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class BrandPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        $response = [
            'id' => $this->data['id'],
            'name' => $this->data['name'],
            'image' => $this->repository->model()->getFirstMediaUrl('brands'),
        ];

        if (!$request->isIndexRequest()) {
            $response['filters'] = $this->getter($request, 'filter-groups');
        }

        return $response;
    }
}


