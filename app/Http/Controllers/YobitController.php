<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YobitController extends Controller
{

    public function index()
    {

        $responseJson_str = file_get_contents(__DIR__.'/../../../public/coin.json');
        $response = json_decode($responseJson_str, true);
        $publicApi = new YobitPublicApiController();
        $getNamesPairs = array_keys($publicApi->getPairsBTC($publicApi->getPairs($response)));
        $getArrayNames = array_chunk($getNamesPairs, 49);
        $pumpCoins =[];

        foreach ($getArrayNames as $item) {
            $getStringNamePairs = implode('-', $item);
            //последние сделки
            $getTradesDeals = $publicApi->getTrades($getStringNamePairs);

            //получить активные ордера
            $getActiveOrders = $publicApi->getDepths($getStringNamePairs);
            //получить активные коины
            $getActiveCoin = $this->getActiveCoin($getTradesDeals);
            //получить коины которыми закупаются
            $getPumpCoin = $this->getPumpCoin($getActiveCoin);
            //var_dump($getPumpCoin);die();
            //получить активные ордера по коинам которые пампят
            $getActiveOrdersByPump = $this->getActiveOrdersByPump($getPumpCoin);
            //Вывести информацию на экран
            //$echoPumpCoin = $this->echoPumpCoin($getPumpCoin, $getActiveOrdersByPump);
            $pumpCoins[] = [$getPumpCoin, $getActiveOrdersByPump];
            ob_flush();
            flush();
            sleep(2);

            //$publicApi->flush_buffers();
        }
        return  view('welcome', ['pumpCoins' =>$pumpCoins]);
    }

    public function getActiveCoin($getTradesDeals)
    {

        $date = date_create();
        $activeCoin=[];
        foreach ($getTradesDeals as $key => $value) {
            $countDeals[$key] =[];
            foreach ($value as $v) {
                if ($v['timestamp'] > (date_timestamp_get($date) - 3600)) {
                    $countDeals[$key][] = $v;
                }
                else {
                    break;
                }
            }
            if(count($countDeals[$key]) > 20){
                $activeCoin[$key][]= $countDeals[$key];
            }
            else {
                continue;
            }
        }
        return $activeCoin;
    }

    public function getPumpCoin($getActiveCoin)
    {
        $pumpCoin=[];
        foreach ($getActiveCoin as $key => $deals){
            $countDealsBuy[$key]=[];
            foreach ($deals as $deal){
                foreach ($deal as $d){
                    if($d['type'] == 'bid'){
                        $countDealsBuy[$key][]=$d;
                    }
                    else {
                        break;
                    }

                }

            }
            if(count($countDealsBuy[$key]) > 3) {
                $pumpCoin[$key][]= $countDealsBuy[$key];
            }

        }

        return $pumpCoin;
    }


    public function getActiveOrdersByPump($getPumpCoin)
    {
        $publicApi = new YobitPublicApiController();
        if(!empty($getPumpCoin)){
            $coins = array_keys($getPumpCoin);
            $getStringNamePairs = implode('-', $coins);
            $getActiveOrdersByPump = $publicApi->getDepths($getStringNamePairs);

            return $getActiveOrdersByPump;
        }
        else{
            return false;
        }

    }

    public function echoPumpCoin($getPumpCoin, $getActiveOrdersByPump){


        foreach ($getPumpCoin as $coin => $value){
            foreach($getActiveOrdersByPump as $key => $order){
                echo "Активные пары за последний час " . strtoupper($coin) .  " Закупок подряд - " . count($value[0]);
                echo '<br>';
                echo "Ордеров на Продажу " . count($order['asks']);
                echo '<br>';
                echo "Ордеров на Покупку " . count($order['bids']);
                echo '<br>', '<br>';
            }

        }
        return true;
    }
}
