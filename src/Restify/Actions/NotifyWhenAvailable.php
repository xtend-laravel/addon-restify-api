<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Product;
use XtendLunar\Addons\PageBuilder\Models\Form;

class NotifyWhenAvailable extends Action
{
    public static $uriKey = 'notify-when-available';

    public function handle(ActionRequest $request, Product $models): JsonResponse
    {
        /** @var Form $notifyForm */
        $notifyForm = Form::query()->where('name', 'notify_product_form')->sole();
        $notifyForm->submissions()->create([
            'payload' => $request->toArray(),
        ]);

        return data([
            'message' => 'Thank you for your interest in this product. We will notify you when it becomes available.',
            'data' => $request->toArray(),
        ]);
    }
}
