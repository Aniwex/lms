<?php

namespace App\Resources;

use App\Models\Claim;

/**
 * Класс-ресурс модели "\App\Models\Claim".
 */
class ClaimResource extends AbstractResource
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \League\Fractal\Resource\Item
     */
    public static function make(\Illuminate\Database\Eloquent\Model $model) : \League\Fractal\Resource\Item
    {
        return new \League\Fractal\Resource\Item($model, function(Claim $claim) {
            $data = [
                'id' => $claim->id,
                'datetime' => $claim->datetime?->format('Y-m-d H:i:s'),
                'code' => $claim->code,
                'duration' => [
                    'original' => $claim->duration ?? 0,
                    'formatted' => $claim->duration ? duration((int) $claim->duration) : '—'
                ],
                'phone' => [
                    'original' => $claim->phone,
                    'formatted' => phone($claim->phone)
                ],
                'redirected_to' => $claim->redirected_to,
                'manager' => [
                    'check' => [
                        'text' => Claim::managerCheckValues()[$claim->manager_check],
                        'value' => $claim->manager_check
                    ],
                    'comment' => $claim->manager_comment
                ],
                'client' => [
                    'check' => [
                        'text' => Claim::clientCheckValues()[$claim->client_check],
                        'value' => $claim->client_check
                    ],
                    'comment' => $claim->client_comment
                ],
                'dialog' => $claim->dialog ?? [],
                'data' => $claim->data ?? [],
            ];

            if ($claim->relationLoaded('source')) {
                $data['source'] = SourceResource::makeArray($claim->source);
            }

            if ($claim->relationLoaded('project')) {
                $data['project'] = ProjectResource::makeArray($claim->project);
            }

            if ($claim->relationLoaded('tags')) {
                $data['tags'] = TagCollection::makeArray($claim->tags);
            }

            return $data;
        });
    }
}
