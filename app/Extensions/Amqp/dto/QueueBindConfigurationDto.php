<?php


namespace App\Extensions\Amqp\dto;


use PhpAmqpLib\Wire\AMQPTable;

/**
 * Класс, который содержит конфигурацию, необходимую
 * при создании связи между маршрутизатором и очередью
 *
 * @package application\extensions\amqp\dto
 */
class QueueBindConfigurationDto
{
    /** @var $route string */
    public string $route;

    /** @var $nowait bool */
    public bool $nowait;

    /** @var $arguments AMQPTable|array */
    public AMQPTable|array $arguments;

    /** @var $ticket int|null */
    public int|null $ticket;

    public function __construct(
        $route = '',
        $nowait = false,
        $arguments = array(),
        $ticket = null
    )
    {
        $this->route = $route;
        $this->nowait = $nowait;
        $this->arguments = $arguments;
        $this->ticket = $ticket;
    }
}
