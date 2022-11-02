<?php

namespace App\Models;

use App\Models\Traits\HasProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Источник обращения (заявки).
 *
 * @property int $id
 * @property string|null $name Наименование
 * @property string|null $code код
 * @property array|null $data Данные
 * @property int $project_id ID проекта
 * @property int $integration_id ID интеграции
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Integration $integration Проект
 * @property-read \App\Models\Project $project Интеграция
 * @method static \Illuminate\Database\Eloquent\Builder|Source byProject(?int $projectId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Source newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Source newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Source query()
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereIntegrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Source whereUpdatedAt($value)

 * @mixin \Eloquent
 */
class Source extends Model
{
    use HasFactory, HasProject;

    /**
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * @var array
     */
    protected $fillable = ['name', 'code', 'data', 'project_id', 'integration_id'];

    /**
     * Привязка к интеграции.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function integration() 
    {
        return $this->belongsTo(Integration::class);
    }
}
