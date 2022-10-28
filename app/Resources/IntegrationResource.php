<?php

namespace App\Resources;

use App\Models\Integration;

/**
 * Класс-ресурс модели "\App\Models\Integration".
 */
class IntegrationResource extends AbstractResource
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \League\Fractal\Resource\Item
     */
    public static function make(\Illuminate\Database\Eloquent\Model $model) : \League\Fractal\Resource\Item
    {
        return new \League\Fractal\Resource\Item($model, function(Integration $integration) {
            return [
                'id' => $integration->id,
                'title' => $integration->title,
                'slug' => $integration->slug,
                'config' => $integration->config
            ];
        });
    }
}
