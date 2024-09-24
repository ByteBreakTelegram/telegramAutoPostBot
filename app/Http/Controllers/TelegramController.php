<?php

namespace App\Http\Controllers;

use App\Telegram\TelegramHandler;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Log;
use TelegramBot\Api\Client;


class TelegramController extends BaseController
{
    protected Client $bot;

    public function __construct()
    {
        $this->bot = new Client(config('telegram.client.token'));

        /** @var TelegramHandler $telegramHandler */
        $telegramHandler = app(TelegramHandler::class);
        $telegramHandler->execute($this->bot);
    }

    public function handle(Request $request)
    {
        $this->bot->run();
        return response()->json(['status' => 'handled']);
    }
}
