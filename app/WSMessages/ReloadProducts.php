<?php

namespace App\WSMessages;

use App\Extensions\Centrifuge\Messages\MessageInterface;

class ReloadProducts implements MessageInterface
{

    public function getMessage(): array|string|int|bool
    {
        return 'need reload';
    }
}
