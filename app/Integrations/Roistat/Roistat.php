<?php

namespace App\Integrations\Roistat;

use App\Integrations\BaseIntegration;

/**
 * Интеграция Roistat
 */
class Roistat extends BaseIntegration
{
    /**
     * @return array Поля для источника, связанные с интеграцией
     */
    public function fields() : array 
    {
        return [
            [
                'type'        => 'text',
                'key'         => 'roistat_id',
                'value'       => 'Roistat ID'
            ],
        ];
    }
}
