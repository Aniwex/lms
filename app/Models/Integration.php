<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Интеграция.
 *
 * @property int $id
 * @property string|null $title Название
 * @property string $slug Код
 * @property array $config Настройки
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Integration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Integration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Integration query()
 * @method static \Illuminate\Database\Eloquent\Builder|Integration whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Integration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Integration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Integration whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Integration whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Integration whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Integration extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $casts = [
        'config' => 'array',
    ];
}
