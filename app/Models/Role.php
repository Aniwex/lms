<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Роль пользователя.
 *
 * @property int $id
 * @property string $slug уникальный текстовый идентификатор
 * @property string $title Наименование
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users Пользователи которые имеют данную роль
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereTitle($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    use HasFactory;

    /**
     * @var string Администратор
     */
    public const SUPER_ADMIN_ID = 1;

    /**
     * @var string Менеджер
     */
    public const MANAGER_ID = 2;
    
    /**
     * @var string Клиент
     */
    public const CLIENT_ID = 3;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Связь с пользователями.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() 
    {
        return $this->belongsToMany(User::class);
    }
}
