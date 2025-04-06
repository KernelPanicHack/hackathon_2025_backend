<?php

namespace App\Extensions\Amqp\cli;

use App\Extensions\Amqp\consumers\ConsumerTrip;
use App\Extensions\Amqp\consumers\ConsumerWater;
use App\Extensions\Amqp\handlers\exceptions\DbExceptionHandler;
use App\Extensions\Amqp\consumers\Consumer;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume {method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Работа со слушателями rabbitmq. Доступные методы: "queueSlow" и "queueCommon"';

    public function handle()
    {
        $param = ucfirst($this->argument('method'));
        $method = "action$param";
        if (!method_exists($this, $method)) {
            echo "Такого метода нет\n";
            return Command::INVALID;
        }
        $this->$method();

        return Command::SUCCESS;
    }

    /**
     * Прослушивание очереди, где происходят "тяжелые" операции
     */
    public function actionQueueSlow()
    {
        try {
            $consumer = new Consumer('slow', array(new DbExceptionHandler()));
            $consumer->consume();
        } catch (Exception $exception) {
            Log::warning($exception->getMessage());
        }
    }

    /**
     * Прослушивание очереди, где происходят "тяжелые" операции
     */
    public function actionQueueCommon()
    {
        try {
            $consumer = new Consumer('common', array(new DbExceptionHandler()));
            $consumer->consume();
        } catch (Exception $exception) {
            Log::warning($exception->getMessage());
        }
    }
}
