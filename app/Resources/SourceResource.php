<?php

namespace App\Resources;

use App\Models\Source;

/**
 * Класс-ресурс модели "\App\Models\Source".
 */
class SourceResource extends AbstractResource
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \League\Fractal\Resource\Item
     */
    public static function make(\Illuminate\Database\Eloquent\Model $model) : \League\Fractal\Resource\Item
    {
        return new \League\Fractal\Resource\Item($model, function(Source $source) {
            $data = [
                'id' => $source->id,
                'name' => $source->name,
                'code' => $source->code,
                'data' => $source->data,
                'created_at' => $source->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $source->updated_at->format('Y-m-d H:i:s')
            ];

            if ($source->relationLoaded('project')) {
                $data['project'] = ProjectResource::makeArray($source->project);
            }

            if ($source->relationLoaded('integration')) {
                $data['integration'] = IntegrationResource::makeArray($source->integration);
            }

            return $data;
        });
    }
}
