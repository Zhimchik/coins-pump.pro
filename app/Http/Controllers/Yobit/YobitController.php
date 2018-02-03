<?php

namespace App\Http\Controllers\Yobit;

use App\Http\Controllers\Controller;
use App\PumpCoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YobitController extends Controller
{

    public function index()
    {

        $responseJson_str = file_get_contents(__DIR__ . '/../../../../public/coin.json');
        $response = json_decode($responseJson_str, true);
        $publicApi = new YobitPublicApiController();
        $getNamesPairs = array_keys($publicApi->getPairsBTC($publicApi->getPairs($response)));
        $getArrayNames = array_chunk($getNamesPairs, 49);
        $pumpCoins =[];
        DB::table('pump_coins')->truncate();

        foreach ($getArrayNames as $item) {
            $getStringNamePairs = implode('-', $item);
            //последние сделки
            $getTradesDeals = $publicApi->getTrades($getStringNamePairs);
            sleep(2);
            //получить активные ордера
            $getActiveOrders = $publicApi->getDepths($getStringNamePairs);
            sleep(2);
            //получить статистику за 24 часа
            $getTickersPairs= $publicApi->getTickers($getStringNamePairs);
            //получить активные коины
            $getActiveCoin = $this->getActiveCoin($getTradesDeals);
            //получить коины которыми закупаются
            $getPumpCoin = $this->getPumpCoin($getActiveCoin);
            //var_dump($getPumpCoin);die();
            //получить активные ордера по коинам которые пампят
            $getActiveOrdersByPump = $this->getActiveOrdersByPump($getPumpCoin);

            //Вывести информацию на экран
            $echoPumpCoin = $this->echoPumpCoin($getPumpCoin, $getActiveOrdersByPump,$getTickersPairs);
            $pumpCoins[] = [$getPumpCoin, $getActiveOrdersByPump];
            ob_flush();
            flush();

        }
        return  'Ok';
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

    public function echoPumpCoin($getPumpCoin, $getActiveOrdersByPump,$getTickersPairs){

        foreach($getPumpCoin as $coin => $value) {
            //ордера на продажу
            $asks = $getActiveOrdersByPump[$coin]['asks'];
            //ордера на покупку
            $bids = $getActiveOrdersByPump[$coin]['bids'];

            if(empty($bids)){
                $bids = array('0');
            }
            if(empty($asks)){
                $asks = array('0');
            }

            $pumpCoin = new PumpCoin;
            $pumpCoin->pairs = strtoupper($coin);
            $pumpCoin->purchase = count($value[0]);
            $pumpCoin->for_purchase = count($bids);
            $pumpCoin->for_sale = count($asks);
            $pumpCoin->high = $getTickersPairs[$coin]['high'];
            $pumpCoin->low = $getTickersPairs[$coin]['low'];
            $pumpCoin->avg = $getTickersPairs[$coin]['avg'];
            $pumpCoin->vol = $getTickersPairs[$coin]['vol'];
            $pumpCoin->vol_cur = $getTickersPairs[$coin]['vol_cur'];
            $pumpCoin->last = $getTickersPairs[$coin]['last'];
            $pumpCoin->buy = $getTickersPairs[$coin]['buy'];
            $pumpCoin->sell = $getTickersPairs[$coin]['sell'];
            $pumpCoin->save();
        }


        return true;
    }
}
