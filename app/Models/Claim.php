<?php

namespace App\Models;

use App\Models\Traits\HasProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Заявка (обращение).
 *
 * @property int $id
 * @property string $code уникальный код
 * @property \Illuminate\Support\Carbon $datetime дата и время заявки
 * @property int|null $duration продолжительность
 * @property int $source_id источник ID
 * @property string $phone номер телефона
 * @property string|null $redirected_to перенаправлен на
 * @property string $manager_check Признак целевого (по мнению менеджера)
 * @property string $client_check Признак целевого (по мнению клиента)
 * @property string|null $manager_comment Комментарий менеджера
 * @property string|null $client_comment Комментарий клиента
 * @property array|null $dialog Диалог
 * @property array|null $data Прочие данные
 * @property int $project_id ID проекта
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Project $project Проект к которому относится заявка
 * @property-read \App\Models\Source $source Источник заявки
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags Теги
 * @property-read int|null $tags_count кол-во тегов
 * @method static \Illuminate\Database\Eloquent\Builder|Claim byProject(?int $projectId = null) получить заявки по проекту
 * @method static \Illuminate\Database\Eloquent\Builder|Claim byUser($value) получить заявки пользователя
 * @method static \Illuminate\Database\Eloquent\Builder|Claim newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Claim newQuery()
 * @method static \Illuminate\Database\Query\Builder|Claim onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Claim query()
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereClientCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereClientComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereDialog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereManagerCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereManagerComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereRedirectedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Claim whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Claim withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Claim withoutTrashed()
 * @mixin \Eloquent
 */
class Claim extends Model
{
    use HasFactory, SoftDeletes, HasProject;

    /**
     * @var array
     */
    protected $casts = [
        'datetime'  => 'datetime:Y-m-d H:i:s',
        'dialog'    => 'array',
        'data' => 'array',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'project_id', 'datetime', 'duration', 'phone', 'source_id', 'redirected_to', 'manager_check', 'manager_comment', 'client_check', 'client_comment'
    ];

    public static function boot()
    {
        parent::boot();
        // при создании нового объекта генерируем уникальный код
        static::creating(function ($object) {
            $object->code = $object->generateRandomCode();
        });
    }

    /**
     * Удаление заявки.
     * @return bool|null
     */
    public function delete() 
    {
        // отвязываем теги
        $this->tags()->detach();

        return parent::delete();
    }

    /**
     * Сохранение значения поля "телефон" (пользователь) в заявке.
     * Сохраняем только цифры.
     *
     * @param mixed $value
     */
    public function setPhoneAttribute($value)
    {
        $value = strval(preg_replace('/\D/', '', $value));

        // если первая 8, то заменяем ее на 7
        if ($value[0] == '8') {
            $value[0] = '7';
        }

        $this->attributes['phone'] = $value;
    }

    /**
     * Поиск по пользователю.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $value)
    {
        /** @todo + поиск по email */
        return $query->where('phone', 'like', '%' . preg_replace('/\D/', '', $value) .'%');
    }

    /**
     * Теги обращения.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags() 
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Источник обращения.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source() 
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Генерация случайного значения для кода обращения.
     * @return string
     */
    private function generateRandomCode() 
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 8; ++$i) {
            $random = str_shuffle($chars);
            $code .= $random[0];
        }
        $code = $this->project_id . $code;

        if ($this->whereCode($code)->count() > 0) {
            return $this->generateRandomCode();
        }

        return $code;
    }

    /**
     * Проверка менеджером: варианты выбора.
     * @return array
     */
    public static function managerCheckValues() 
    {
        return [
            'targeted' => 'целевой',
            'untargeted' => 'нецелевой',
            'unidentified' => 'не установленный'
        ];
    }

    /**
     * Проверка клиентом: варианты выбора.
     */
    public static function clientCheckValues() 
    {
        return [
            'targeted' => 'целевой',
            'untargeted' => 'нецелевой',
            'unchecked' => 'не проверенный'
        ];
    }
}
