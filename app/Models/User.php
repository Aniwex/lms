<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Пользователь системы.
 *
 * @property int $id
 * @property string $login логин
 * @property string $password Пароль
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $role_id ID роли
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects Проекты, с которыми связан пользователь
 * @property-read int|null $projects_count
 * @property-read \App\Models\Role $role Роль пользователя
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Удаление пользователя.
     * @return bool|null
     */
    public function delete() 
    {
        // отвязываем проекты
        $this->projects()->detach();

        return parent::delete();
    }

    /**
     * Связь с ролью.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role() 
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Связь с проектами.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects() 
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    /**
     * Является ли пользователь администратором.
     * @return bool
     */
    public function isAdmin() 
    {
        return $this->role_id === Role::SUPER_ADMIN_ID;
    }

    /**
     * Является ли пользователь менеджером проекта.
     * @param Project|int $project Проект (сущность или ID).
     * @return bool
     */
    public function isProjectManager($project) 
    {
        return $this->isManager() && $this->projects()->get()->contains($project);
    }

    /**
     * Имеет ли пользователь роль менеджер.
     * @return bool
     */
    public function isManager() 
    {
        return $this->role_id === Role::MANAGER_ID;
    }

    /**
     * Имеет ли пользователь хотя бы один проект.
     * @return bool
     */
    public function hasProjects() 
    {
        return $this->projects()->count() > 0;
    }

    /**
     * Является ли пользователь менеджером с хотя бы одним закрепленным проектом.
     * @return bool
     */
    public function isManagerWithProjects() 
    {
        return $this->isManager() && $this->hasProjects();
    }

    /**
     * Имеет ли пользователь роль клиент.
     * @return bool
     */
    public function isClient() 
    {
        return $this->role_id === Role::CLIENT_ID;
    }
}
