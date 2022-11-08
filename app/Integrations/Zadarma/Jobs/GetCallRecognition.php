<?php

namespace App\Integrations\Zadarma\Jobs;

use App\Integrations\Zadarma\Exceptions\BadApiConnection;
use App\Integrations\Zadarma\Exceptions\InvalidRecognition;
use App\Models\Claim;
use App\Models\Tag;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Zadarma_API\Client as ZadarmaApi;
use Illuminate\Support\Str;

/**
 * Получение распознавания звонка через апи задармы. Выполняется после хука задармы в нашу систему, когда запись звонка и текст распознавания готовы.
 */
class GetCallRecognition
{
    use Dispatchable, SerializesModels;

    /**
     * @var ZadarmaApi Объект для работы с АПИ задармы.
     */
    private $api;

    /**
     * @var Claim Обращение для которого получаем распознавание диалога.
     */
    private $claim;

    /**
     * @var mixed ID звонка в системе задармы.
     */
    private $callId;

    /**
     * @var \Psr\Log\LoggerInterface Объект для логирования информации в файл (zadarma.log).
     */
    private \Psr\Log\LoggerInterface $logger;

    /**
     * Create a new job instance.
     *
     * @param mixed $callId Идентификатор звонка в системе задармы.
     *
     * @return void
     */
    public function __construct($callId)
    {
        // находим обращение по идентификатору звонка из задармы
        $this->claim = Claim::whereJsonContains('data->zadarma_call_id', $callId)->firstOrFail();

        // проверяем наличие источника обращения
        if (!$this->claim->source->count() || !isset($this->claim->source->data['api_key']) || !isset($this->claim->source->data['api_secret'])) {
            throw new BadApiConnection();
        }
        // устанавливаем подключение к API (берем данные из источника обращения)
        $this->api = new ZadarmaApi($this->claim->source->data['api_key'], $this->claim->source->data['api_secret']);

        // сохраняем переданный идентификатор звонка
        $this->callId = $callId;

        // определяем каннал логирования
        $this->logger = Log::channel('zadarma');
    }

    /**
     * Валидация полученного ответа от задармы.
     *
     * @param array $result
     * @throws InvalidRecognition
     */
    private function validate($result) {

        if (
            !isset($result['status']) || $result['status'] !== 'success' ||
            !isset($result['recognitionStatus']) || $result['recognitionStatus'] !== 'recognized' ||
            !isset($result['phrases']) || !is_array($result['phrases'])
        )
            throw new InvalidRecognition();
    }

    /**
     * Проверяет полученный диалог на валидность и форматирует к нужному формату для сохранения в базу данных.
     * @param array $dialog
     * @return array
     */
    private function checkDialog(array $dialog) {

        $formatted = [];

        foreach ($dialog as $item) {

            if (!isset($item['result']) || !isset($item['channel']))
                throw new InvalidRecognition();

            $formatted[] = [
                'result' => is_array($item['result']) ? $item['result'] : [$item['result']],
                'channel' => $item['channel']
            ];
        }

        return $formatted;
    }

    /**
     * Разделить диалог на два массива: все слова клиента и все слова оператора.
     * @param array $dialog
     * @return array
     */
    private function splitDialog(array $dialog)
    {
        $clientWords = collect();
        $operatorWords = collect();

        foreach ($dialog as $phrase) {

            $words = collect(explode(" ", Str::lower($phrase['result'])));

            if ($phrase['channel'] == 1) {
                $operatorWords = $operatorWords->merge($words);
            } else {
                $clientWords = $clientWords->merge($words);
            }
        }
        return array($clientWords->unique(), $operatorWords->unique());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->logger->debug('Попытка получить распознавание диалога...');

        // делаем запрос в апи задармы, получаем распознавание диалога
        $result = json_decode($this->api->call('/v1/speech_recognition/', [
            'call_id' => $this->callId
        ]), true);

		$this->logger->debug('Ответ от задармы: ');
		$this->logger->debug($result);

        // валидируем полученный ответ
        $this->validate($result);

        // сохраняем диалог
        $this->claim->dialog = $this->checkDialog($result['phrases']);

        // определяем тэги
        list($clientWords, $operatorWords) = $this->splitDialog($result['phrases']);
        $tags = Tag::recognizeFromDialog($clientWords, $operatorWords)->pluck('id')->toArray();

        // привязываем теги к обращению
        $this->claim->tags()->sync($tags);

        // сохраняем обращение
        $this->claim->save();

        $this->logger->debug('Распознавание диалога успешно сохранено.');
    }
}
