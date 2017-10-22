<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/16
 * Time: 22:12
 */

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Train;

class ScheduleController extends Controller
{
    public function getSeatCount(Request $request){
        $from_station = $request->input('from_station');
        $to_station = $request->input('to_station');
        $date = $request->input('date');
        $from_stations = Schedule::model()->getStationName($from_station);
        $to_stations = Schedule::model()->getStationName($to_station);
        $trains = Schedule::model()->getStationNo($from_stations);
        $train_list = [];
        foreach ($trains as $train){
            $result = Schedule::model()->getStationNo($to_stations, ['train_id' => $train->train_id]);
            if (!empty($result[0]))
                if ($result[0]->station_no > $train->station_no){
                    $train_list[$train->train_id]['from_station'] = $train->station_name;
                    $train_list[$train->train_id]['to_station'] = $result[0]->station_name;
                    $train_list[$train->train_id]['from_station_no'] = $train->station_no;
                    $train_list[$train->train_id]['to_station_no'] = $result[0]->station_no;
                    $train_list[$train->train_id]['start_time'] = $train->start_time;
                    $train_list[$train->train_id]['arrive_time'] = $result[0]->arrive_time;
                    $train_list[$train->train_id]['price'] = $this->getSeatPrice($train->train_id, $train->station_no, $result[0]->station_no);
                }
        }
        $data_list = [];
        foreach ($train_list as $key => $t){
            $where = [
                ['station_no', '>=', $t['from_station_no']],
                ['station_no', '<=', $t['to_station_no']],
                ['train_id', $key]
                ];
            $data[$key]['train_id'] = $key;
            $data[$key]['from_station_no'] = $t['from_station_no'];
            $data[$key]['to_station_no'] = $t['to_station_no'];
            $data[$key]['seat_count'] = Schedule::model()->selectSeatCount($where);
            $data[$key]['train_info'] = Train::model()->getTrainInfo($key);
            $data[$key]['start_time'] = $t['start_time'];
            $data[$key]['arrive_time'] = $t['arrive_time'];
            $days =  $data[$key]['seat_count'][0]->days;
            $data[$key]['day'] = $days;

            $total_time = strtotime($t['arrive_time'] . " + $days day") - strtotime($t['start_time']);
            $total_day = (int)date("d", $total_time);
            $total_hour = (int)date("H", $total_time) + ($total_day - 1) * 24;
            $total_minute = date("i", $total_time);

            $data[$key]['total_time'] = $total_hour . "时" . $total_minute . "分";
            $data[$key]['from_station'] = $t['from_station'];
            $data[$key]['to_station'] = $t['to_station'];
            $data[$key]['price'] = $t['price'];
            $data_list[] = $data[$key];
        }
        return ResponseHelper::getInstance()->jsonResponse(0, $data_list, '余票数据');
    }

    public function getSchedule(Request $request){
        $train_id = $request->input('train_id');
        $from_station_no = $request->input('from_station_no');
        $to_station_no = $request->input('to_station_no');
        $schedule = Schedule::model()->getSchedule($train_id);
        foreach ($schedule as $s){
            if ($s->station_no <= $to_station_no and $s->station_no >= $from_station_no)
                $s->is_pass = 1;
            else
                $s->is_pass = 0;
        }
        return ResponseHelper::getInstance()->jsonResponse(0, $schedule, '时刻表');
    }

    public function getSeatPrice($train_id, $from_station_no, $to_station_no){
        $res = Schedule::model()->getPrice($train_id, $from_station_no, $to_station_no);
        $price = [];
        foreach ($res as $v)
            $price[] = explode('|', $v->seat_price);
        $total_price = array(0, 0, 0, 0, 0, 0, 0);
        foreach ($price as $p){
            for($i=0;$i<7;$i++)
                $total_price[$i] += (int)$p[$i];
        }
        return $total_price;
    }
}