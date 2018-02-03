<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 30.01.18
 * Time: 15:30
 */

namespace App\Http\Controllers;


use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController
{
    public function index(){
        $response = Telegram::getMe();

        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();
        var_dump($username);
    }

    public function show()
    {
        Telegram::setAsyncRequest(true)
            ->sendPhoto(['chat_id' => 'CHAT_ID', 'photo' => 'path/to/photo.jpg']);

    }

}