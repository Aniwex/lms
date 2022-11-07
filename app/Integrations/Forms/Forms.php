<?php

namespace App\Integrations\Forms;

use App\Integrations\BaseIntegration;

/**
 * Интеграция с формами обратной связи.
 */
class Forms extends BaseIntegration
{
    /**
     * @return array Поля для источника, связанные с интеграцией
     */
    public function fields() : array 
    {
        return [
            [
                'type'    => 'hint',
                'message' => 'ID (уникальный идентификатор текущего источника) указывается как параметр "source_id" при отправке запроса с формы обратной связи.'
            ]
        ];
    }
}
