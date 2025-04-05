<?php


namespace App\Http\Controllers\Api;


use App\Extensions\Centrifuge\Centrifuge;
use App\Extensions\DataProviders\Providers\ArrayDataProvider;
use App\Helpers\FormatterResponse;
use Illuminate\Support\Facades\Auth;

class WSController
{
    public function getToken()
    {
        /** @var Centrifuge $client */
        $client = app(Centrifuge::class);

        $userId = Auth::user() ? Auth::user()->id : 'anonymous';

        $dataProvider = new ArrayDataProvider([
            'token' => $client->makeToken($userId)
        ]);

        return response()->json(
            FormatterResponse::format($dataProvider)
        );
    }
}
