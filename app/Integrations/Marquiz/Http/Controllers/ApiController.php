<?php

namespace App\Integrations\Marquiz\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Integrations\Marquiz\Http\Requests\NewQuizRequest;
use App\Models\Claim;
use App\Models\Source;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Контроллер для взаимодействия с внешними вызовами МАРКВИЗА.
 */
class ApiController extends Controller
{
    /**
     * @var LoggerInterface Объект для логирования результатов интеграции.
     */
    protected LoggerInterface $logger;

    /**
     * @var array Данные полученные из марквиза.
     */
    protected array $request;

    /**
     * Инициализация контроллера. Определяем канал логирования.
     */
    public function __construct()
    {
        $this->logger = Log::channel('marquiz');
    }

    /**
     * Обработка хука из системы марквиза. Оповещает о новой заявке.
     * @param NewQuizRequest $request Запрос, полученный из марквиза.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleHook(NewQuizRequest $request) {

        try {
            $this->request = $request->validated();

            $this->logger->debug('Получен запрос из марквиза...');
            $this->logger->debug(print_r($this->request, true));

            // находим источник в нашей системе
            $source = Source::whereJsonContains('data->quiz_id', $this->request['quiz']['id'])->firstOrFail();

            // создаем обращение
            $claim = new Claim();

            $claim->datetime = \Carbon\Carbon::create($this->request['created'])->setTimezone('Europe/Moscow')->toDateTimeString();
            $claim->phone = $this->request['contacts']['phone'];

            $claim->source_id = $source->id;
            $claim->project_id = $source->project_id;

            $claim->data = $this->formatExtraDataArray();

            $claim->save(); // сохраняем обращение

            $this->logger->debug('Было успешно создано обращение! ID = #'.$claim->id);

            return Response::success()->message('OK');

        } catch (\Exception $ex) {

            // обрабатываем ошибки
            $this->logger->error("Ошибка при обработке хука из марквиза.");
            $this->logger->error($ex->getMessage());

            return Response::error(($ex instanceof HttpResponseException) ? $ex->getCode() : 500)
                ->message('Произошла непредвиденная ошибка.');
        }
    }

    /**
     * Форматирование массива, который содержит дополнительную информацию.
     * @return array
     */
    private function formatExtraDataArray()
    {
        $data = [];

        // ответы на вопросы
        foreach ($this->request['answers'] as $index => $item) {

            $question = 'Вопрос: ' . PHP_EOL . $item['q'];
            $answers = 'Ответ:' . PHP_EOL . (is_array($item['a']) ? implode(', ', array_values($item['a'])) :  $item['a']);

            $data['question_' . ($index + 1)] = implode(PHP_EOL, [$question, $answers]);
        }

        // контактная информация
        if (isset($this->request['contacts']['name']) && !empty($this->request['contacts']['name'])) {
            $data['name'] = $this->request['contacts']['name'];
        }
        if (isset($this->request['contacts']['email']) && !empty($this->request['contacts']['email'])) {
            $data['email'] = $this->request['contacts']['email'];
        }

        // название квиза
        if (isset($this->request['quiz']['name']) && !empty($this->request['quiz']['name'])) {
            $data['quiz_name'] = $this->request['quiz']['name'];
        }

        // дополнительная информация (урл, реферер, ip)
        if (isset($this->request['extra']['href']) && !empty($this->request['quiz']['href'])) {
            $data['href'] = $this->request['extra']['href'];
        }
        if (isset($this->request['extra']['referrer']) && !empty($this->request['quiz']['referrer'])) {
            $data['referrer'] = $this->request['extra']['referrer'];
        }
        if (isset($this->request['extra']['ip']) && !empty($this->request['quiz']['ip'])) {
            $data['ip'] = $this->request['extra']['ip'];
        }

        // utm метки
        if (isset($this->request['extra']['utm']) && !empty($this->request['extra']['utm'])) {
            foreach ($this->request['extra']['utm'] as $utmkey => $utmval) {
                if (strpos($utmkey, 'utm') === false) {
                    $utmkey = 'utm_' . $utmkey;
                }
                $data[$utmkey] = $utmval;
            }
        }

        return $data;
    }
}
