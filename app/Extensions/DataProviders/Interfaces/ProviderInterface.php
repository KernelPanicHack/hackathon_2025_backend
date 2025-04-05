<?php

namespace App\Extensions\DataProviders\Interfaces;

interface ProviderInterface
{
    public function getData() : array|object;
}
