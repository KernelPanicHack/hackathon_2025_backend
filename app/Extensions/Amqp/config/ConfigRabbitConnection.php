<?php

namespace App\Extensions\Amqp\config;

/**
 * Класс конфига по подключению к кролику
 */
class ConfigRabbitConnection
{
    protected string $login;
    protected string $password;
    protected string $host;
    protected string $port;
    protected string $vhost;

    public function __construct(string $host, string $port, string $login, string $password, string $vhost)
    {
        $this->host = $host;
        $this->port = $port;
        $this->login = $login;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getVhost(): string
    {
        return $this->vhost;
    }
}
