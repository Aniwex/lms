<?php

namespace App\Resources;

use App\Models\Tag;
use App\Models\TagWord;
use Illuminate\Support\Str;

/**
 * Класс-ресурс модели "\App\Models\Tag".
 */
class TagResource extends AbstractResource
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \League\Fractal\Resource\Item
     */
    public static function make(\Illuminate\Database\Eloquent\Model $model) : \League\Fractal\Resource\Item
    {
        return new \League\Fractal\Resource\Item($model, function(Tag $tag) {
            $data = [
                'id' => $tag->id,
                'name' => $tag->name,
                'objective' => (bool) $tag->objective,
            ];

            if ($tag->relationLoaded('project')) {
                $data['project'] = ProjectResource::makeArray($tag->project);
            }

            foreach (TagWord::$types as $wordType) {
                $wordType = $wordType . 's';
                $relation = Str::camel($wordType);
                if ($tag->relationLoaded($relation)) {
                    $data[$wordType] = $tag->$relation->pluck('phrase');
                }
            }

            return $data;
        });
    }
}
