<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/19
 * Time: 10:21
 */

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\Seat;

class SeatController extends Controller
{
    //找到符合条件的座位
    public function FindQualifiedSeat($train_id, $seat_type, $from_station_no, $to_station_no){
//        $train_id = $request['train_id'];
//        $seat_type = $request['seat_type'];
//        $from_station_no = $request['from_station_no'];
//        $to_station_no = $request['to_station_no'];
        $seats = Seat::model()->getSeatInfo($train_id, $seat_type);
        $free_seats = [];
        $front_free_seats = [];
        $back_free_seats = [];
        $fb_free_seats = [];
        $qualified_seat = null;
        foreach ($seats as $seat){
            if((int)$seat->is_free != 0) {//优先选取不全空的座位
                $rest = substr($seat->is_free, $from_station_no - 1, $to_station_no - $from_station_no);
                if((int)$rest == 0) //符合条件
                    if ($from_station_no !=1){
                        $r = substr($seat->is_free, $from_station_no - 2, 1);
                        if((int)$r == 1) {//优先选取出发站的前一站座位已被占的座位
                            if((int)substr($seat->is_free, $to_station_no - 1, 1) == 1)//优先选取到达站的后一站座位已被占的座位
                                $qualified_seat = $seat;
                            else
                                $back_free_seats[] = $seat;
                        }
                        else {
                            if((int)substr($seat->is_free, $to_station_no - 1, 1) == 1)
                                $front_free_seats[] = $seat;
                            else
                                $fb_free_seats[] = $seat;
                        }

                    } else{
                        if ((int)substr($seat->is_free, $to_station_no - 1, 1) == 1)
                            $qualified_seat = $seat;
                        else
                            $back_free_seats[] = $seat;
                    }
            } else
                $free_seats[] = $seat;
        }

        if(empty($qualified_seat)){
            if(!empty($back_free_seats[0]))
                $qualified_seat = $back_free_seats[0];
            elseif (!empty($front_free_seats[0]))
                $qualified_seat = $front_free_seats[0];
            elseif (!empty($fb_free_seats[0]))
                $qualified_seat = $fb_free_seats[0];
            elseif (!empty($free_seats[0]))
                $qualified_seat = $free_seats[0];
        }
        return $qualified_seat;
    }

    //确定座位占用后更改该座位占用信息
    public function updateSeatInfo($seat_id, $seat_is_free, $from_station_no, $to_station_no, $operate=1){
        $station_num = $to_station_no - $from_station_no;
        $str = "";
        for($i=0; $i < $station_num; $i ++)
            $str .= "$operate";
        $updated_is_free = substr_replace($seat_is_free, $str, $from_station_no-1, $station_num);
        $result = Seat::model()->updateSeatInfo($seat_id, ['is_free' => $updated_is_free]);
        return $result;
    }


}