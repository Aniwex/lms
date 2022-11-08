<?php

namespace App\Integrations\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Integrations\Forms\Http\Requests\SiteFormRequest;
use App\Models\Claim;
use App\Models\Source;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Контроллер для взаимодействия с внешними вызовами от форм обратной связи на сайтах.
 */
class ApiController extends Controller
{
    /**
     * @var LoggerInterface Объект для логирования результатов интеграции.
     */
    protected LoggerInterface $logger;

    /**
     * Инициализация контроллера. Определяем канал логирования.
     */
    public function __construct()
    {
        $this->logger = Log::channel('forms_integration');
    }

    /**
	 * Обработка запроса из формы обратной связи.
     * @param SiteFormRequest $request Запрос, полученный из сайта.
     *
     * @return \Illuminate\Http\Response
	 */
    public function handleFormRequest(SiteFormRequest $request) {

        try {
            $this->logger->debug('Получен запрос из формы обратной связи...');
            $this->logger->debug(print_r($request->validated(), true));

            // находим источник в нашей системе
            $source = Source::findOrFail($request->source_id);

            // создаем обращение
            $claim = new Claim();

            $claim->datetime = \Carbon\Carbon::now()->toDateTimeString(); // в качестве даты, берем текущее значение
            $claim->phone = $request->phone;

            $claim->source_id = $source->id;
            $claim->project_id = $source->project_id;

            $claim->data = $request->data;

            $claim->save(); // сохраняем обращение

            $this->logger->debug('Было успешно создано обращение! ID #'.$claim->id);

            return Response::success()->message('OK');

        } catch (\Exception $ex) {

            // обрабатываем ошибки
            $this->logger->error("Ошибка при обработке запроса из формы обратной связи.");
            $this->logger->error($ex->getMessage());

            return Response::error(($ex instanceof HttpResponseException) ? $ex->getCode() : 500)
                ->message('Произошла непредвиденная ошибка.');
        }
    }
}
