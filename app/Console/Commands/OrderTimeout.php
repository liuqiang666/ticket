<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Seat;
use App\Models\Schedule;
use App\Http\Controllers\SeatController;

class OrderTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OrderTimeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tmp_time = date('Y-m-d H:i:s', time() - 60 * 15);
        //订单状态0未支付
        $orders = Order::model()->getRows([['order_status', 0], ['order_time', '<=', $tmp_time]], 'id, train_id, seat_type, from_station_no, to_station_no');
        foreach ($orders as $order){
            //订单状态5已失效
            $r = Order::model()->updateOrderInfo(['order_status' => 5], ['id' => $order->id]);
            if($r){
                $tickets = Ticket::model()->getRows(['order_id' => $order->id], 'id, seat_id');//订单下的车票
                foreach ($tickets as $ticket){
                    $seat = Seat::model()->findRow(['id' => $ticket->seat_id], 'is_free');//获得座位信息
                    $seatController = new SeatController();
                    //修改车票状态为已废弃3
                    $t_res = Ticket::model()->updateTicketInfo(['ticket_status' => 3], ['id' => $ticket->id]);
                    //将座位信息恢复
                    $s_res = $seatController->updateSeatInfo($ticket->seat_id, $seat->is_free, $order->from_station_no, $order->to_station_no, 0);
                    //将余票信息恢复
                    Schedule::model()->updateTicketCount($order->train_id, $order->seat_type, $order->from_station_no, $order->to_station_no, 1);
                    $this->info(" ticket id:" . $t_res . " seat id:". $s_res);
                }
            }
        }
        return false;
    }
}
