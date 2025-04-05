<?php

namespace App\Extensions\Amqp\consumers;

use App\Extensions\Amqp\AmqpRabbitMQ;
use App\Extensions\Amqp\dto\ConsumeConfigurationDto;
use App\Extensions\Amqp\handlers\exceptions\ExceptionHandlerInterface;
use App\Extensions\Amqp\helpers\MessageSerializer;
use App\Extensions\Amqp\helpers\QueueHelper;
use App\Extensions\Amqp\jobs\JobInterface;
use App\Jobs\GpsTripJob;
use App\Models\Trips\Trip;
use App\Models\Trips\TripFile;
use App\Models\Spr\SprFileExtension;
use App\Models\Spr\SprFileType;
use App\Models\Spr\SprLevelPollution;
use App\Models\Spr\SprStatus;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumerTrip
{
    /**
     * Массив обработчиков исключений. В случае если возникнет исключение
     * во время исполнения очереди, то будут выполнены обработчики исключений
     *
     * @var ExceptionHandlerInterface[]
     */
    public $handlersExceptions;

    /**
     * Название очереди, с которой будут считываться сообщения
     *
     * @var string
     */
    public $queue;

    public function __construct($queue, array $handlersExceptions = [])
    {
        $this->queue = $queue;
        $this->handlersExceptions = $handlersExceptions;
    }

    /**
     * Метод выполняет работу слушателя очереди указанной в конструкторе.
     */
    public function consume()
    {
        /** @var  $msg AMQPMessage */
        $callback = function (AMQPMessage $msg) {
            try {
                $item = json_decode($msg->body);
                $file = base64_decode($item->file);
                $tripId = $item->tripId;
                $pollutionCode = $item->level_impurity;

                $pollution = SprLevelPollution::query()->where('code', $pollutionCode)->first();

                $trip = Trip::query()->find($tripId);
                if ($pollution) {
                    $trip->level_pollution_id = $pollution->id;
                    $trip->save();
                }

                $typeFileAr = SprFileType::query()
                    ->where('code', 'result')
                    ->first();

                $oldFile = TripFile::query()
                    ->where('file_type_id', $typeFileAr->id)
                    ->where('trip_id', $tripId)
                    ->first();

                if ($oldFile && Storage::disk('tripFiles')->exists($oldFile->path)) {
                    Storage::disk('tripFiles')->delete($oldFile->path);
                }

                $fileExtension = SprFileExtension::query()->where('code', 'xlsx')->first();
                $fileName = time() . 'result.xlsx';
                $date = date('Y-m-d', strtotime($trip->date_trip));
                $path = "$date/$trip->id/";

                if (!$fileExtension) {
                    abort(403, 'Данное расширение файла не доступно');
                }

                $fullPath = $path . $fileName;

                Storage::disk('tripFiles')->put($fullPath, $file);

                $fileAr = new TripFile();
                $fileAr->file_name = $fileName;
                $fileAr->path = $fullPath;
                $fileAr->file_type_id = $typeFileAr->id;
                $fileAr->trip_id = $trip->id;
                $fileAr->file_extension_id = $fileExtension->id;
                $fileAr->size = Storage::disk('tripFiles')->size($fullPath);
                $fileAr->save();

                QueueHelper::addToSlowQueue(
                    MessageSerializer::serializeMessage(new GpsTripJob($fullPath, $trip)
                    ), 'slow.php.gps', true);

                $msg->ack();
            } catch (Exception $exception) {
                echo $exception->getMessage();
                $msg->reject(false);
            }
        };

        Log::info('Успешное подключение к rabbitmq, прослушивается очередь - ' . $this->queue);
        $amqp = App::make(AmqpRabbitMQ::class);
        $amqp->consume($this->queue, $callback, new ConsumeConfigurationDto());
    }
}
