<?php

namespace App\Integrations\Roistat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Integrations\Roistat\Http\Requests\NewRoistatRequest;
use App\Models\Claim;
use App\Models\Integration;
use App\Models\Source;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Контроллер для взаимодействия с внешними вызовами Ройстата.
 */
class ApiController extends Controller
{
    /**
     * @var LoggerInterface Объект для логирования результатов интеграции.
     */
    protected $logger;

    /**
     * @var array Данные полученные из марквиза.
     */
    protected $request;

    /**
     * @var int ID интеграции
     */
    protected int $integration;

    /**
     * Инициализация контроллера. Определяем канал логирования.
     */
    public function __construct()
    {
        $this->logger = Log::channel('roistat');
        $this->integration = Integration::whereSlug('roistat')->firstOrFail()?->id;
    }

    /**
     * Обработка хука из системы Roistat. Оповещает о новом лиде.
     * @param NewRoistatRequest $request Запрос, полученный из Roistat.
     * @param mixed $roistat_id
     *
     * @return \Illuminate\Http\Response
     */
    public function handleHook(NewRoistatRequest $request, $roistat_id) {
        try {
            $this->request = $request->validated();

            $this->logger->debug('Получен запрос из Roistat...');
            $this->logger->debug(print_r($this->request, true));

            // находим источник в нашей системе
            $source = Source::whereIntegrationId($this->integration)
                ->whereJsonContains('data->roistat_id', $roistat_id)
                ->firstOrFail();

            // создаем обращение
            $claim = new Claim();

            $claim->datetime = \Carbon\Carbon::create($request['date'])->setTimezone('Europe/Moscow')->toDateTimeString();
            $claim->phone = $request['phone'];

            $claim->source_id = $source->id;
            $claim->project_id = $source->project_id;
   
            $claim->data = $this->formatExtraDataArray($request);

            $claim->save(); // сохраняем обращение

            $this->logger->debug('Было успешно создано обращение! ID = #'.$claim->id);

            return Response::success()->message('OK');

        } catch (\Exception $ex) {

            // обрабатываем ошибки
            $this->logger->error("Ошибка при обработке хука из Roistat.");
            $this->logger->error($ex->getMessage());

            return Response::error(($ex instanceof HttpResponseException) ? $ex->getCode() : 500)
                ->message('Произошла непредвиденная ошибка.');
        }
    }

    /**
     * Форматирование массива, который содержит дополнительную информацию.
     * @param NewRoistatRequest $request
     * @return array
     */
    private function formatExtraDataArray(NewRoistatRequest $request)
    {
        $data = [];

        // контактная информация
        if (isset($request['name']) && !empty($request['name'])) {
            $data['name'] = $request['name'];
        }
        if (isset($request['visit_id']) && !empty($request['visit_id'])) {
            $data['visit_id'] = $request['visit_id'];
        }

        // дополнительная информация (урл, реферер, ip)
        if (isset($request['page']) && !empty($request['page'])) {
            $data['page'] = $request['page'];
        }
        if (isset($request['marker']) && !empty($request['marker'])) {
            $data['marker'] = $request['marker'];
        }
        if (isset($request['city']) && !empty($request['city'])) {
            $data['city'] = $this->request['city'];
        }
        if (isset($request['country']) && !empty($request['country'])) {
            $data['country'] = $this->request['country'];
        }
        if (isset($request['ip']) && !empty($request['ip'])) {
            $data['ip'] = $request['ip'];
        }
        if (isset($request['referrer']) && !empty($request['referrer'])) {
            $data['referrer'] = $request['referrer'];
        }
        if (isset($request['domain']) && !empty($request['domain'])) {
            $data['domain'] = $request['domain'];
        }
        if (isset($request['landing_page']) && !empty($request['landing_page'])) {
            $data['landing_page'] = $request['landing_page'];
        }
        if (isset($request['first_visit']) && !empty($request['first_visit'])) {
            $data['first_visit'] = $request['first_visit'];
        }

        // utm метки
        if (isset($request['utm_source']) && !empty($request['utm_source'])) {
            $data['utm']['source'] = $request['utm_source'];
        }
        if (isset($request['utm_medium']) && !empty($request['utm_medium'])) {
            $data['utm']['medium'] = $request['utm_medium'];
        }
        if (isset($request['utm_campaign']) && !empty($request['utm_campaign'])) {
            $data['utm']['campaign'] = $request['utm_campaign'];
        }
        if (isset($request['utm_term']) && !empty($request['utm_term'])) {
            $data['utm']['term'] = $request['utm_term'];
        }
        if (isset($request['utm_content']) && !empty($request['utm_content'])) {
            $data['utm']['content'] = $request['utm_content'];
        }

        //roistat param
        if (isset($request['roistat_param_1']) && !empty($request['roistat_param_1'])) {
            $data['roistat']['param_1'] = $request['roistat_param_1'];
        }
        if (isset($request['roistat_param_2']) && !empty($request['roistat_param_2'])) {
            $data['roistat']['param_2'] = $request['roistat_param_2'];
        }
        if (isset($request['roistat_param_3']) && !empty($request['roistat_param_3'])) {
            $data['roistat']['param_3'] = $request['roistat_param_3'];
        }
        if (isset($request['roistat_param_4']) && !empty($request['roistat_param_4'])) {
            $data['roistat']['param_4'] = $request['roistat_param_4'];
        }
        if (isset($request['roistat_param_5']) && !empty($request['roistat_param_5'])) {
            $data['roistat']['param_5'] = $request['roistat_param_5'];
        }

        //другие параметры
        if (isset($request['google_client_id']) && !empty($request['google_client_id'])) {
            $data['another']['google_client_id'] = $request['google_client_id'];
        }
        if (isset($request['metrika_client_id']) && !empty($request['metrika_client_id'])) {
            $data['another']['metrika_client_id'] = $request['metrika_client_id'];
        }
        if (isset($request['source_level_1']) && !empty($request['source_level_1'])) {
            $data['another']['source_level_1'] = $request['source_level_1'];
        }
        if (isset($request['source_level_2']) && !empty($request['source_level_2'])) {
            $data['another']['source_level_2'] = $request['source_level_2'];
        }

        return $data;
    }
}