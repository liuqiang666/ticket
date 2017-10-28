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
use App\Models\Ticket;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function generateOrder(Request $request){
        $data = $request->except('passengers');
        $passengers = $request->input('passengers');
//        print_r("passengers:" . $passengers);
        $passengers = json_decode($passengers, true);
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
            return ResponseHelper::getInstance()->jsonResponse(0, ['result' => $result, 'passengers' => $passengers], "order");
        }
        return ResponseHelper::getInstance()->jsonResponse(1100, ['result' => $result, 'passengers' => $passengers], "generate order error");
    }


    public function getOrderInfo(Request $request){
        $user_id = $request->input('user_id');
        $where = [];
        if($request->has('order_status')) {
            $order_status = $request->input('order_status');
            $where = ['order_status' => $order_status];
        }

        $orders = Order::model()->getOrderInfo($user_id, $where);
        $data = [];
        foreach ($orders as $order){
            $tickets = Ticket::model()->getTicketInfo(['order_id' => $order->order_id]);
            $d['order'] = $order;
            $d['ticket'] = $tickets;
            $data[] = $d;
        }
        return ResponseHelper::getInstance()->jsonResponse(0, $data, "order info");
    }

    public function changeOrderStatus(Request $request){
        $order_id = $request->input('order_id');
//        $order_status = $request->input('order_status');
        //1代表已支付
        $res = Order::model()->updateOrderInfo(['order_status' => 1], ['id' => $order_id]);
        if($res){
            $tickets = Ticket::model()->getRows(['order_id' => $order_id], 'id as ticket_id');
            foreach ($tickets as $ticket)
                Ticket::model()->updateTicketInfo(['ticket_status' => 1], ['id' => $ticket->ticket_id]);
            return ResponseHelper::getInstance()->jsonResponse(0, [$res], "update order status success");
        }

        return ResponseHelper::getInstance()->jsonResponse(1100, [$order_id], "update order status error");
    }


}