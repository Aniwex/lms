<?php

namespace App\Models;

use App\Models\Traits\HasProject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Тег обращения.
 *
 * @property int $id
 * @property string $name Название
 * @property bool $objective Целевой (да/нет)
 * @property int $project_id ID проекта
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TagWord[] $clientMinusWords Минус-слова клиента
 * @property-read int|null $client_minus_words_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TagWord[] $clientPlusWords Плюс-слова клиента
 * @property-read int|null $client_plus_words_count
 * @property-read string $client_minus_words
 * @property-read string $client_plus_words
 * @property-read string $operator_minus_words
 * @property-read string $operator_plus_words
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TagWord[] $operatorMinusWords Минус-слова оператора
 * @property-read int|null $operator_minus_words_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TagWord[] $operatorPlusWords Плюс-слова оператора
 * @property-read int|null $operator_plus_words_count
 * @property-read \App\Models\Project $project Проект
 * @method static Builder|Tag byProject(?int $projectId = null)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static \Illuminate\Database\Query\Builder|Tag onlyTrashed()
 * @method static Builder|Tag query()
 * @method static Builder|Tag recognizeFromDialog($clientWords, $operatorWords)
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereDeletedAt($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereObjective($value)
 * @method static Builder|Tag whereProjectId($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Tag withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Tag withoutTrashed()
 * @mixin \Eloquent
 */
class Tag extends Model
{
    use HasFactory, HasProject, SoftDeletes;

    public static function boot()
    {
        parent::boot();

        self::deleting(function($model){
            // при удалении тега удаляем все его слова из базы данных
            if ($model->forceDeleting) {
                TagWord::whereTagId($model->id)->delete();
            }
        });
    }
    /**
     * Переопределяем метод сохранения объекта в базу данных.
     * Используем отдельную таблицу для хранения плюс-минус слов клиента и оператора.
     *
     * @return bool
     */
    public function save(array $options = []) 
    {
        // удаляем все слова связанные с тегом, если это редактирование
        if ($this->id)
            TagWord::whereTagId($this->id)->delete();

        $wordsToCreate = [];

        // сохраняем слова в отдельный массив, попутно убираем эти значения из атрибутов модели
        foreach (TagWord::$types as $type) {
            $key = $type . 's';
            if (!empty($this->attributes[$key])) {
                $wordsToCreate[$type] = preg_split('/\r\n|\r|\n/', $this->attributes[$key]);
            }
            unset($this->attributes[$key]);
        }

        // после сохранения модели, имея ID тэга, создаем и связываем плюс-минус слова
        if (parent::save()) {
            foreach ($wordsToCreate as $type => $phrases) {
                foreach ($phrases as $word) {
                    TagWord::create(['phrase' => $word, 'type' => $type, 'tag_id' => $this->id]);
                }
            }
        }

        return true;
    }

    /**
     * Найти теги по заданному диалогу (тексту).
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param array $clientWords Слова клиента
     * @param array $operatorWords Слова оператора
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecognizeFromDialog(Builder $query, $clientWords, $operatorWords)
    {
        return $query->where(function(Builder $query) use ($clientWords, $operatorWords) {

            $query->whereHas('clientPlusWords', function(Builder $query) use ($clientWords) {
                $query->whereIn('phrase', $clientWords);
            })->orWhereHas('operatorPlusWords', function(Builder $query) use ($operatorWords) {
                $query->whereIn('phrase', $operatorWords);
            });

        })->where(function($query) use ($clientWords, $operatorWords) {

            $query->whereDoesntHave('clientMinusWords', function(Builder $query) use ($clientWords) {
                $query->whereIn('phrase', $clientWords);
            })->whereDoesntHave('operatorMinusWords', function(Builder $query) use ($operatorWords) {
                $query->whereIn('phrase', $operatorWords);
            });

        });
    }

    /**
     * Плюс-слова клиента.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientPlusWords() 
    {
        return $this->hasMany(TagWord::class)->where('type', 'client_plus_word');
    }

    /**
     * @return string Плюс-слова клиента в виде текста для редактирования на форме.
     */
    public function getClientPlusWordsAttribute() 
    {
        return $this->clientPlusWords()->get()->pluck('phrase')->implode(PHP_EOL);
    }

    /**
     * Минус-слова клиента.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clientMinusWords() 
    {
        return $this->hasMany(TagWord::class)->where('type', 'client_minus_word');
    }

    /**
     * @return string Минус-слова клиента в виде текста для редактирования на форме.
     */
    public function getClientMinusWordsAttribute() 
    {
        return $this->clientMinusWords()->get()->pluck('phrase')->implode(PHP_EOL);
    }

    /**
     * Плюс-слова оператора.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operatorPlusWords()
    {
        return $this->hasMany(TagWord::class)->where('type', 'operator_plus_word');
    }

    /**
     * @return string Плюс-слова оператора в виде текста для редактирования на форме.
     */
    public function getOperatorPlusWordsAttribute() 
    {
        return $this->operatorPlusWords()->get()->pluck('phrase')->implode(PHP_EOL);
    }

    /**
     * Минус-слова оператора.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operatorMinusWords() 
    {
        return $this->hasMany(TagWord::class)->where('type', 'operator_minus_word');
    }

    /**
     * @return string Минус-слова клиента в виде текста для редактирования на форме.
     */
    public function getOperatorMinusWordsAttribute() 
    {
        return $this->operatorMinusWords()->get()->pluck('phrase')->implode(PHP_EOL);
    }
}
