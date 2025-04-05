<?php


namespace App\Extensions\Amqp\dto;


use PhpAmqpLib\Wire\AMQPTable;

/**
 * Класс, который содержит конфигурацию, необходимую
 * при создании слушателя очереди
 *
 * @package application\extensions\amqp\dto
 */
class ConsumeConfigurationDto
{
    /** @var $consumerTag string */
    public string $consumerTag;

    /** @var $noLocal bool */
    public bool $noLocal;

    /** @var $noAckItem bool */
    public bool $noAckItem;

    /** @var $exclusive bool */
    public bool $exclusive;

    /** @var $noWait bool */
    public bool $noWait;

    /** @var $ticket int|null */
    public int|null $ticket;

    /** @var $arguments AMQPTable|array */
    public AMQPTable|array $arguments;

    public function __construct(
        $consumerTag = '',
        $noLocal = false,
        $noAckItem = false,
        $exclusive = false,
        $noWait = false,
        $ticket = null,
        $arguments = array()
    )
    {
        $this->consumerTag = $consumerTag;
        $this->noLocal = $noLocal;
        $this->noAckItem = $noAckItem;
        $this->exclusive = $exclusive;
        $this->noWait = $noWait;
        $this->ticket = $ticket;
        $this->arguments = $arguments;
    }
}
