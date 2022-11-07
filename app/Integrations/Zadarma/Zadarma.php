<?php

namespace App\Integrations\Zadarma;

use App\Integrations\BaseIntegration;
use App\Models\Integration;
use App\Models\Source;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use Zadarma_API\Client as ZadarmaAPI;

/**
 * Интеграция Zadarma
 */
class Zadarma extends BaseIntegration
{
    /**
     * @var LoggerInterface Объект для логирования результатов интеграции.
     */
    protected LoggerInterface $logger;

    /**
     * @var Integration Интеграция
     */
    protected Integration $integration;

    /**
     * Инициализация контроллера. Определяем канал логирования.
     * @param Source $source Источник обращения
     */
    public function __construct(Source $source)
    {
        parent::__construct($source);
        $this->logger = Log::channel('zadarma');
        $this->integration = Integration::whereSlug('zadarma')->firstOrFail();
    }

    /**
     * @return array Поля для источника, связанные с интеграцией
     */
    public function fields() : array 
    {
        return [
            [
                'type'    => 'text',
                'key'     => 'api_key',
                'value'   => 'API KEY',
            ],
            [
                'type'    => 'text',
                'key'     => 'api_secret',
                'value'   => 'API SECRET',
            ],
            [
                'type'    => 'hint',
                'message' => 'Нужно обязательно указать API доступы, чтобы получить список телефонных номеров',
            ],
            [
                'type'    => 'select',
                'key'     => 'phone',
                'value'   => 'Номер телефона',
                'options' => $this->phones()
            ]
        ];
    }

    /**
     * Получение доступных вариантов для выбора для селекта номеров телефона.
     * @return array
     */
    private function phones() : array
    {
        try {
            // обращение в апи
            $api = new ZadarmaAPI($this->source->data['api_key'], $this->source->data['api_secret']);
            $result = json_decode($api->call('/v1/direct_numbers/'), true);

            if ($result['status'] && isset($result['info'])) {

                $options = [];

                foreach ($result['info'] as $item) {
                    // формируем массив где ключ это номер телефона, а название: номер + описание
                    if (isset($item['number']) && isset($item['number_name'])) {

                        // проверяем что номер уже не используется где либо
                        if (
                            Source::whereIntegrationId($this->integration->id)->whereJsonContains('data->phone', $item['number'])->count() == 0
                            || (isset($this->source->data['phone']) && $this->source->data['phone'] == $item['number'])
                        ) {
                            $options[$item['number']] = phone($item['number']) . ' (' . $item['number_name'] . ')';
                        }
                    }
                }

                return $options;
            }

            $this->logger->error("Ошибка при получении номеров из задармы. Api метод: /v1/direct_numbers/.", $result);

            return [];
        }
        catch(\Exception $ex) {
            $this->logger->error("Ошибка при получении номеров из задармы. Api метод: /v1/direct_numbers/");
            $this->logger->error($ex);
            return [];
        }
    }

}
