<?php

namespace App\Telegram;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Class HelpCommand.
 */
class TestCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'get_pairs';

    /**
     * @var string Command Description
     */
    protected $description = 'Получить Пары';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
       $this->replyWithChatAction(['action' => Actions::TYPING]);
        $pumpCoin = \App\PumpCoin::latest()->first();
        $this->replyWithMessage(['text' => trans('telegram.active_pairs') . $pumpCoin->pairs.'***']);
        $text  = sprintf('%s: %s'.PHP_EOL, trans('telegram.purchase'), $pumpCoin->purchase);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.for_purchase'), $pumpCoin->for_purchase);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.for_sale'), $pumpCoin->for_sale);
        $this->replyWithMessage(compact('text'));

        $this->replyWithMessage(['text' => trans('telegram.info_pairs') . $pumpCoin->pairs .'***']);
        $text  = sprintf('%s: %s'.PHP_EOL, trans('telegram.high'), $pumpCoin->high);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.low'), $pumpCoin->low);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.avg'), $pumpCoin->avg);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.vol'), $pumpCoin->vol);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.vol_cur'), $pumpCoin->vol_cur);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.last'), $pumpCoin->last);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.buy'), $pumpCoin->buy);
        $text .= sprintf('%s: %s'.PHP_EOL, trans('telegram.sell'), $pumpCoin->sell);
        $this->replyWithMessage(compact('text'));
        return;

    }
}
