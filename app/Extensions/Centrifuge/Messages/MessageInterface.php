<?php


namespace App\Extensions\Centrifuge\Messages;


interface MessageInterface
{
    public function getMessage(): array|string|int|bool;
}
