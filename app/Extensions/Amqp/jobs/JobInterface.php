<?php


namespace App\Extensions\Amqp\jobs;


interface JobInterface
{
    /**
     * Этот метод вызывается в воркере
     */
    public function execute();

    /**
     * Метод производить запись в логи
     *
     * @param string $msg
     */
    public function log($msg);

}
