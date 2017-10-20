<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/19
 * Time: 15:47
 */

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Order;
use App\Models\Schedule;
use App\Models\Ticket;
use Illuminate\Http\Request;


class TicketController extends Controller
{
    public function generateTicket(Request $request){
        $order_id = $request['order_id'];
        $order = Order::model()->findRow(['id' => $order_id]);
        if(!$order)
            return ResponseHelper::getInstance()->jsonResponse(1104, [$order_id]);
        $passengers = Order::model()->getPassengerOfOrder($order_id);
        $tickets = [];
        foreach ($passengers as $passenger){
            $seatController = new SeatController();
            $seat = $seatController->FindQualifiedSeat($order->train_id, $order->seat_type, $order->from_station_no, $order->to_station_no);
            if(empty($seat))
                return ResponseHelper::getInstance()->jsonResponse(1103, [$order]);
            $data['passenger_id'] = $passenger->passenger_id;
            $data['train_code'] = $order->train_code;
            $data['from_station'] = $order->from_station_name;
            $data['to_station'] = $order->to_station_name;
            $data['seat_id'] = $seat->id;
            $data['ticket_price'] = $passenger->price;
            $data['ticket_type'] = $passenger->passenger_type;
            $data['ticket_start_time'] = $order->start_time;
            $data['order_id'] = $order_id;
            $result = Ticket::model()->insertRow($data);//用事务合适
            if($result){
                $res = $seatController->updateSeatInfo($seat->id, $seat->is_free, $order->from_station_no, $order->to_station_no);
                if($res)
                    Schedule::model()->updateTicketCount($order->train_id, $order->seat_type, $order->from_station_no, $order->to_station_no);
            }
            $tickets[] = $result;
        }
        return ResponseHelper::getInstance()->jsonResponse(0, $tickets, "generate ticket success");
    }
}