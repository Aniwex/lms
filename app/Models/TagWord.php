<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Плюс или минус слово в теге.
 *
 * @property int $id
 * @property string $phrase Фраза
 * @property string $type Тип
 * @property int|null $tag_id ID тега
 * @property-read \App\Models\Tag|null $tag тег
 * @method static \Illuminate\Database\Eloquent\Builder|TagWord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TagWord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TagWord query()
 * @method static \Illuminate\Database\Eloquent\Builder|TagWord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TagWord wherePhrase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TagWord whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TagWord whereType($value)
 * @mixin \Eloquent
 */
class TagWord extends Model
{
    use HasFactory;

    /**
     * @var array Доступные типы фраз.
     */
    public static $types = ['client_plus_word', 'client_minus_word', 'operator_plus_word', 'operator_minus_word'];

    /**
     * @var array
     */
    public $fillable = ['phrase', 'type', 'tag_id'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Связь с тэгом.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tag() 
    {
        return $this->belongsTo(Tag::class);
    }
}
