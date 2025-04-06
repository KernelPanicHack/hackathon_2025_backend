<?php

namespace App\Observers;


use App\Models\Operation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OperationsObserver
{
    /**
     * Handle the Operations "created" event.
     */
    public function created(Operation $operations): void
    {
        $user = User::query()->find($operations->user_id)->first();

        $balance = $operations->remaining_balance;
        if ($balance < $user->money/3) {
            $messageContent = 'Ваш любимый банк';
            $recomend = $user->money/3;
            // Отправка сообщения
            Mail::raw($messageContent, function ($message) use ($user, $recomend) {
                $message->to($user->email)
                    ->subject("Ваш баланс меньше финансовой подушки в 3 раза. Рекомендуемый баланс $recomend");
            });
        }

    }

    /**
     * Handle the Operations "updated" event.
     */
    public function updated(Operation $operations): void
    {
        //
    }

    /**
     * Handle the Operations "deleted" event.
     */
    public function deleted(Operation $operations): void
    {
        //
    }

    /**
     * Handle the Operations "restored" event.
     */
    public function restored(Operation $operations): void
    {
        //
    }

    /**
     * Handle the Operations "force deleted" event.
     */
    public function forceDeleted(Operation $operations): void
    {
        //
    }
}
