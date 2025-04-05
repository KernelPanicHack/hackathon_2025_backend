<?php


namespace App\Extensions\Amqp\dto;


use PhpAmqpLib\Wire\AMQPTable;

/**
 * Класс, который содержит конфигурацию, необходимую
 * при удалении связей между маршрутизатором и очередью
 *
 * @package application\extensions\amqp\dto
 */
class QueueUnBindConfigurationDto
{
    /** @var $route string */
    public string $route;

    /** @var $arguments AMQPTable|array */
    public AMQPTable|array $arguments;

    /** @var $ticket int|null */
    public int|null $ticket;

    public function __construct(
        $route = '',
        $arguments = array(),
        $ticket = null
    )
    {
        $this->route = $route;
        $this->arguments = $arguments;
        $this->ticket = $ticket;
    }
}
