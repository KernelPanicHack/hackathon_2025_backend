<?php

namespace App\Extensions\DataProviders\Providers;

use App\Extensions\DataProviders\Interfaces\ProviderInterface;

class ArrayDataProvider implements ProviderInterface
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
