<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Проект.
 *
 * @property int $id
 * @property string $name Название
 * @property string $domain Главный домен
 * @property array $mirrows Список зеркал
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags Теги
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users Пользователи связанные с проектом
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Project my()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Query\Builder|Project onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereMirrows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Project withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Project withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Project withAnyTagsOfAnyType($tags)
 * @mixin \Eloquent
 */
class Project extends Model
{
    use HasFactory, \Spatie\Tags\HasTags;

    /**
     * @var array
     */
    protected $casts = [
        'mirrows' => 'array'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'domain', 'mirrows'
    ];

    /**
     * Удаление проекта.
     * @return bool|null
     */
    public function delete() 
    {
        // отвязываем пользователей
        $this->users()->detach();

        // удаляем теги
        $this->tags()->delete();

        return parent::delete();
    }

    /**
     * Проекты текущего авторизованного пользователя.
     * Администратору доступы все проекты.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMy($query) 
    {
        $user = user();

        if (!$user->isAdmin()) {
            $query->whereHas('users', function($q) use ($user) {
                $q->where('id', $user->id);
            });
        }

        return $query;
    }

    /**
     * Связь с пользователями.
     */
    public function users() 
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Связь с тегами.
     */
    public function tags() 
    {
        return $this->hasMany(Tag::class);
    }
}
