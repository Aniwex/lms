<?php

namespace App\Integrations\Marquiz;

use App\Integrations\BaseIntegration;

/**
 * Класс для интеграции с марквизом. Реализует логику работы с данными для источника из марквиза.
 */
class Marquiz extends BaseIntegration
{
    /**
     * @return array Поля для источника, связанные с интеграцией
     */
    public function fields() : array
    {
        return [
            [
                'type'        => 'text',
                'key'         => 'quiz_id',
                'name'        => 'QUIZ ID',
                'description' => 'Идентификатор квиза. Извлекается из урла на странице с квизом. Например: https://panel.marquiz.ru/quizzes/607444fda3f3f400447a0482/edit'
            ]
        ];
    }
}
