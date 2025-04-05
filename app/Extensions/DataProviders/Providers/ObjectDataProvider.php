<?php

namespace App\Extensions\DataProviders\Providers;

use App\Extensions\DataProviders\Interfaces\ProviderInterface;

class ObjectDataProvider implements ProviderInterface
{

    public $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }

    public function getData(): array|object
    {
        return $this->data;
    }
}
