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
use App\Models\Schedule;
use App\Http\Controllers\TicketController;
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
            $tickets = [];
            foreach ($passengers as $passenger) {
                $d = [
                    'passenger_id' => $passenger['id'],
                    'order_id' => $result,
                    'passenger_type' => $passenger['type'],
                    'price' => $passenger['price']
                ];
                $r = Order::model()->addRelationPassengerOrder($d);
                $order = Order::model()->findRow(['id' => $result]);
                if($r){
                    $seatController = new SeatController();
                    $seat = $seatController->FindQualifiedSeat($order->train_id, $order->seat_type, $order->from_station_no, $order->to_station_no);
                    if(empty($seat))
                        return ResponseHelper::getInstance()->jsonResponse(1103, [$order]);
                    $t_data['passenger_id'] = $passenger['id'];
                    $t_data['train_code'] = $data['train_code'];
                    $t_data['from_station'] = $data['from_station_name'];
                    $t_data['to_station'] = $data['to_station_name'];
                    $t_data['seat_id'] = $seat->id;
                    $t_data['ticket_price'] = $passenger['price'];
                    $t_data['ticket_type'] = $passenger['type'];
                    $t_data['ticket_start_time'] = $data['start_time'];
                    $t_data['order_id'] = $result;
                    $t_result = Ticket::model()->insertRow($t_data);//用事务合适
                    if($t_result){
                        $res = $seatController->updateSeatInfo($seat->id, $seat->is_free, $order->from_station_no, $order->to_station_no);
                        if($res)
                            Schedule::model()->updateTicketCount($order->train_id, $order->seat_type, $order->from_station_no, $order->to_station_no);
                    }
                    $tickets[] = $result;
                } else
                    return ResponseHelper::getInstance()->jsonResponse(1100, $result, "order_passenger add error");
            }
            return ResponseHelper::getInstance()->jsonResponse(0, [$result, $tickets], "order");
        }
        return ResponseHelper::getInstance()->jsonResponse(1100, [$result], "generate order error");
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