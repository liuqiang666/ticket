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
use App\Models\Seat;
use Illuminate\Http\Request;


class TicketController extends Controller
{
    public function generateTicket(Request $request){
        $order_id = $request->input('order_id');
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

    //退票
    public function refundTicket(Request $request){
        $ticket_id = $request->input('ticket_id');

        $ticket = Ticket::model()->findRow(['id' => $ticket_id], 'seat_id, ticket_price, ticket_start_time, order_id, ticket_status');
        $order = Order::model()->findRow(['id' => $ticket->order_id], 'train_id, seat_type, from_station_no, to_station_no');
        $seat = Seat::model()->findRow(['id' => $ticket->seat_id], 'is_free');//获得座位信息
        $seatController = new SeatController();
        if($ticket->ticket_status == 0){
            //修改车票状态为已退票2
            $t_res = Ticket::model()->updateTicketInfo(['ticket_status' => 2], ['id' => $ticket_id]);
            //将座位信息恢复
            $s_res = $seatController->updateSeatInfo($ticket->seat_id, $seat->is_free, $order->from_station_no, $order->to_station_no, 0);
            //将余票信息恢复
            Schedule::model()->updateTicketCount($order->train_id, $order->seat_type, $order->from_station_no, $order->to_station_no, 1);
            if($t_res and $s_res)
                return ResponseHelper::getInstance()->jsonResponse(0, [$ticket], "refund ticket success");
        } else
            return ResponseHelper::getInstance()->jsonResponse(1100, ["ticket status" => $ticket->ticket_status], "this ticket can't refund");
        return ResponseHelper::getInstance()->jsonResponse(1100, [$ticket], "refund ticket error");
    }
}