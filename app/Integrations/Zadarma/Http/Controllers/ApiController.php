<?php

namespace App\Integrations\Zadarma\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Integrations\Zadarma\Exceptions\InvalidRecognition;
use App\Integrations\Zadarma\Exceptions\BadApiConnection;
use App\Integrations\Zadarma\Http\Requests\NotifyRequest;
use App\Integrations\Zadarma\Jobs\GetCallRecognition;
use App\Models\Claim;
use App\Models\Integration;
use App\Models\Source;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Контроллер для взаимодействия с внешними вызовами ЗАДАРМЫ.
 */
class ApiController extends Controller
{
    /**
     * Идентификатор события окончания звонка.
     */
    private const EVENT_NOTIFY_END = 'NOTIFY_END';

    /**
     * Идентификатор события "запись звонка и распознавание текста готовы".
     */
    private const EVENT_NOTIFY_RECORD = 'NOTIFY_RECORD';

    /**
     * @var LoggerInterface Объект для логирования результатов интеграции.
     */
    protected LoggerInterface $logger;

    /**
     * @var int ID интеграции
     */
    protected int $integration;

    /**
     * Инициализация контроллера. Определяем канал логирования.
     */
    public function __construct()
    {
        $this->logger = Log::channel('zadarma');
        $this->integration = Integration::whereSlug('zadarma')->firstOrFail()?->id;
    }

	/**
	 * Обработка оповещений о событиях из задармы.
     * @param NotifyRequest $request Запрос, полученный из задармы.
     *
     * @return \Illuminate\Http\Response
	 */
    public function handleNotify(NotifyRequest $request)
    {
        try {
            $this->logger->debug('Получен запрос из задармы...');

            // маршрутизируем обработку, в зависимости от указателя на событие
            if ($request->event == self::EVENT_NOTIFY_END) {
                $this->handleNotifyEndEvent($request->validated());

            } elseif ($request->event == self::EVENT_NOTIFY_RECORD) {
                $this->handleNotifyRecordEvent($request->validated());
            }

            return Response::success()->message('OK');

        } catch (\Exception $ex) {

            // обрабатываем ошибки
            $this->logger->error("Ошибка при обработке хука из задармы.");
            $this->logger->error($ex->getMessage());

            return Response::error(($ex instanceof HttpResponseException) ? $ex->getCode() : 500)
                ->message('Произошла непредвиденная ошибка.');
        }
	}

    /**
     * Обработать событие "окончание звонка". Определяем источник и добавляем новое обращение в систему.
     * @param array $data Запрос, полученный из задармы.
     */
    private function handleNotifyEndEvent(array $data)
    {
        $this->logger->debug('Событие: окончание звонка.');
        $this->logger->debug(print_r($data, true));

        // находим источник в нашей системе
        $source = Source::whereIntegrationId($this->integration)->whereJsonContains('data->phone', $data['called_did'])->firstOrFail();

        // создаем обращение
        $claim = new Claim();

        $claim->duration = $data['duration'];
        $claim->datetime = $data['call_start'];
        $claim->phone = $data['caller_id'];

        $claim->source_id = $source->id;
        $claim->project_id = $source->project_id;

        // сохраняем идентификатор для получения распознавания текста, если такое присутствует
        if (isset($data['call_id_with_rec'])) {
            $claim->data = [
                'zadarma_call_id' => $data['call_id_with_rec']
            ];
        }

        // сохраняем обращение
        $claim->save();

        $this->logger->debug('Было успешно создано обращение! ID = #'.$claim->id);
    }

    /**
     * Обработать событие "запись звонка и распознавание текста готовы". Получаем распознавание диалога к обращению и обновляем данные в нашей системе. Сохраняем диалог и подбираем теги к обращению.
     *
     * @param array $data Запрос, полученный из задармы.
     *
     * @throws SavingClaimException Если возникает ошибка при сохранении обращения в систему.
     * @throws InvalidRecognition В случае невалидных данных в массиве с результатами распознавания со стороны задармы.
     * @throws BadApiConnection Если невозможно подключиться к API задармы.
     */
    private function handleNotifyRecordEvent(array $data)
    {
        $this->logger->debug('Событие: запись звонка и распознавание текста готовы.');
        $this->logger->debug(print_r($data, true));

		sleep(120);

        GetCallRecognition::dispatchSync($data['call_id_with_rec']);
    }
}
