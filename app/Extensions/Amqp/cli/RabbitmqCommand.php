<?php

namespace App\Extensions\Amqp\cli;

use App\Extensions\Amqp\AmqpRabbitMQ;
use App\Extensions\Amqp\dto\ExchangeConfigurationDto;
use App\Extensions\Amqp\dto\QueueBindConfigurationDto;
use App\Extensions\Amqp\dto\QueueConfigurationDto;
use App\Extensions\Amqp\dto\QueueUnBindConfigurationDto;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

/**
 * Class RabbitmqCommand
 *
 * Содержит команды для основных операций с rabbitmq
 */
class RabbitmqCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:base {method} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Работа с очередями rabbitmq\n
                              Доступные методы: declareAll, declareExchange, declareQueue,
                              bindQueue, deleteAll, deleteExchange, deleteQueue, unBindQueue";

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
     * Создание структуры очередей для rabbitmq
     * в соответствии с конфигурацией
     *
     */
    public function actionDeclareAll()
    {
        self::actionDeclareExchange();
        self::actionDeclareQueue();
        self::actionBindQueue();
    }

    /**
     * Объявление exchange указанных в конфигурации компонента
     */
    public function actionDeclareExchange()
    {
        try {
            $amqp = App::make(AmqpRabbitMQ::class);
            foreach ($amqp->exchanges as $exchange) {
                $config = new ExchangeConfigurationDto();
                if (key_exists('config', $exchange)) {
                    foreach ($exchange['config'] as $key => $value) {
                        $config->$key = $value;
                    }
                }
                $amqp->declareExchange($exchange['name'], $exchange['type'], $config);
            }
            echo count($amqp->exchanges) . " exchanges created\n";
        } catch (Exception $ex) {
            echo $ex->getMessage() . "\n";
        }
    }

    /**
     * Объявление очередей указанных в конфигурации компонента
     */
    public function actionDeclareQueue()
    {
        try {
            $amqp = App::make(AmqpRabbitMQ::class);
            foreach ($amqp->queues as $queue) {
                $config = new QueueConfigurationDto();
                if (key_exists('config', $queue)) {
                    foreach ($queue['config'] as $key => $value) {
                        $config->$key = $value;
                    }
                }
                $amqp->declareQueue($queue['name'], $config);
            }
            echo count($amqp->queues) . " queues created\n";
        } catch (Exception $ex) {
            echo $ex->getMessage() . "\n";
        }
    }

    /**
     * Создание маршрутов для очередей
     */
    public function actionBindQueue()
    {
        try {
            $amqp = App::make(AmqpRabbitMQ::class);
            foreach ($amqp->bindings as $bind) {
                foreach ($bind['routing'] as $route) {
                    $config = new QueueBindConfigurationDto();
                    $config->route = $route;
                    if (key_exists('config', $bind)) {
                        foreach ($bind['config'] as $key => $value)
                            $config->$key = $value;
                    }
                    $amqp->queueBind($bind['queue'], $bind['exchange'], $config);
                }
            }
            echo count($amqp->bindings) . " binding created\n";
        } catch (Exception $ex) {
            echo $ex->getMessage() . "\n";
        }
    }

    /**
     * Удаление конфигурации в указанном компоненте
     */
    public function actionDeleteAll()
    {
        self::actionUnBindQueue();
        self::actionDeleteExchange();
        self::actionDeleteQueue();
    }

    /**
     * Удаление exchange в указанном компоненте
     */
    public function actionDeleteExchange()
    {
        try {
            $amqp = App::make(AmqpRabbitMQ::class);
            foreach ($amqp->exchanges as $exchange) {
                if (!isset($exchange['name'])) {
                    throw new Exception('Отсутствует атрибут \'name\' exchange');
                }
                $amqp->deleteExchange($exchange['name']);
            }
            echo count($amqp->exchanges) . " exchanges deleted\n";
        } catch (Exception $exception) {
            echo $exception->getMessage() . "\n";
        }
    }

    /**
     * Удаление очередей в указанном компоненте
     */
    public function actionDeleteQueue()
    {
        try {
            $amqp = App::make(AmqpRabbitMQ::class);
            foreach ($amqp->queues as $queue) {
                if (!isset($queue['name'])) {
                    throw new Exception('Отсутствует атрибут \'name\' queue');
                }
                $amqp->deleteQueue($queue['name']);
            }
            echo count($amqp->queues) . " queues deleted\n";
        } catch (Exception $exception) {
            echo $exception->getMessage() . "\n";
        }
    }

    /**
     * Отвязать маршруты от очередей
     */
    public function actionUnBindQueue()
    {
        try {
            $amqp = App::make(AmqpRabbitMQ::class);
            foreach ($amqp->bindings as $bind) {
                foreach ($bind['routing'] as $route) {
                    $amqp->queueUnBind($bind['queue'], $bind['exchange'], new QueueUnBindConfigurationDto($route));
                }
            }
            echo count($amqp->bindings) . " unbinding\n";
        } catch (Exception $ex) {
            echo $ex->getMessage() . "\n";
        }
    }
}
