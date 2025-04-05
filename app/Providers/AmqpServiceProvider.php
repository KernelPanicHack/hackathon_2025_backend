<?php

namespace App\Providers;

use App\Extensions\Amqp\AmqpRabbitMQ;
use App\Extensions\Amqp\config\ConfigRabbitConnection;
use Illuminate\Support\ServiceProvider;

class AmqpServiceProvider extends ServiceProvider
{
    /**
     * Регистрация вызова обертки для работы с очередями
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AmqpRabbitMQ::class, function ($app) {
            $config = new ConfigRabbitConnection(
                env('RABBITMQ_HOST'),
                env('RABBITMQ_PORT'),
                env('RABBITMQ_LOGIN'),
                env('RABBITMQ_PASSWORD'),
                env('RABBITMQ_VHOST')
            );

            $exchanges = [
                [
                    'name' => 'main.topic',
                    'type' => 'topic',
                    'config' => array(
                        'durable' => true,
                        'autoDelete' => false,
                    ),
                ],
            ];

            $queues = [
                [
                    'name' => 'slow',
                    'config' => [
                        'durable' => true,
                        'autoDelete' => false,
                    ],
                ],
                [
                    'name' => 'common',
                    'config' => [
                        'durable' => true,
                        'autoDelete' => false,
                    ],
                ],
            ];

            $bindings = [
                [
                    'queue' => 'slow',
                    'exchange' => 'main.topic',
                    'routing' => array('slow.#')
                ],
                [
                    'queue' => 'common',
                    'exchange' => 'main.topic',
                    'routing' => array('common.#')
                ]
            ];

            return new AmqpRabbitMQ($config, $exchanges, $queues, $bindings);
        });
    }
}
