<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/19
 * Time: 15:00
 */

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function generateOrder(Request $request){
        $data = $request->except('passengers');
        $passengers = $request['passengers'];
        $data['order_time'] = date('Y-m-d H:i:s');
        $result = Order::model()->insertRow($data);
        if($result){
            foreach ($passengers as $passenger) {
                $d = [
                    'passenger_id' => $passenger['id'],
                    'order_id' => $result,
                    'passenger_type' => $passenger['type'],
                    'price' => $passenger['price']
                ];
                $r = Order::model()->addRelationPassengerOrder($d);
                if(!$r)
                    return ResponseHelper::getInstance()->jsonResponse(1100, $result, "order_passenger add error");
            }
            return ResponseHelper::getInstance()->jsonResponse(0, $result, "order");
        }
        return ResponseHelper::getInstance()->jsonResponse(1100, $result, "generate order error");
    }


}