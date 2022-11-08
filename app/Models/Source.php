<?php

namespace App\Models;

use App\Exceptions\BaseAppException;
use App\Integrations\BaseIntegration;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Claim[] $claims Заявки, у которых указан данный источник.
 * @property-read int|null $claims_count
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

    /**
     * Заявки, у которых указан данный источник.
     */
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    /**
     * Получить объект класса интеграции источника.
     * 
     * @throws \App\Exceptions\BaseAppException
     * @return \App\Integrations\BaseIntegration объект класса интеграции источника
     */
    public function getIntegrationClass() : BaseIntegration
    {
        $name = ucfirst(strtolower(trim($this->integration->slug ?? '')));
        $class = '\App\Integrations\\' . $name . '\\' . $name;

        if (!class_exists($class) || $class instanceof BaseIntegration) {
            throw new BaseAppException('Класса интеграции не существует в системе');
        }

        return new $class($this);
    }
}
