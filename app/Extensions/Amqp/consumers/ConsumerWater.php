<?php

namespace App\Extensions\Amqp\consumers;

use App\Extensions\Amqp\AmqpRabbitMQ;
use App\Extensions\Amqp\dto\ConsumeConfigurationDto;
use App\Extensions\Amqp\handlers\exceptions\ExceptionHandlerInterface;
use App\Extensions\Amqp\helpers\MessageSerializer;
use App\Extensions\Amqp\helpers\QueueHelper;
use App\Extensions\Amqp\jobs\JobInterface;
use App\Jobs\CategorizeJob;
use App\Models\Trips\Trip;
use App\Models\Trips\TripFile;
use App\Models\Spr\SprFileExtension;
use App\Models\Spr\SprFileType;
use App\Models\Spr\SprLevelPollution;
use App\Models\Spr\SprStatus;
use App\Models\WaterInfo;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumerWater
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
                $jobUid = $item->jobUid;
                $waterInfo = WaterInfo::query()->where('uid_job', $jobUid)->first();
                $statusFinish = SprStatus::query()->where('code', 'finish')->first();
                $date = date('Y-m-d');
                $path = "water-result/$date/";

                $fullPath = $path . Str::random(32) . '.png';

                Storage::disk('public')->put($fullPath, $file);
                $waterInfo->file_path = $fullPath;
                $waterInfo->status_id = $statusFinish->id;
                $waterInfo->save();
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
