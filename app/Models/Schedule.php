<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/16
 * Time: 18:54
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Schedule extends Model
{
    protected $table = 'schedule';
    public $timestamps = false;
    private static $instance = null;

    public static function model() {
        if (self::$instance == null) {
            self::$instance = new Schedule();
        }
        return self::$instance;
    }

    //查询余票信息
    public function selectSeatCount($where){
        $select = "min(wz_count) as wz, min(yz_count) as yz, min(yw_count) as yw, min(rw_count) as 
        rw, min(ydz_count) as ydz, min(edz_count) as edz, min(swz_count) as swz, sum(is_next_day) as days";
        $result = DB::table($this->table)
            ->select(DB::raw($select))
            ->where($where)
            ->get();
        return $result;
    }

    //获取符合要求的列车id和以及站点在该车次中的station_no
    public function getStationNo($stations, $where=[]){
        $result = DB::table($this->table)
            ->select('train_id', 'station_no', 'arrive_time', 'start_time', 'station_name')
            ->where($where)
            ->where(function ($query) use ($stations){
                foreach ($stations as $station)
                    $query->orwhere('station_name', $station->station_name);
            })
            ->get();
        return $result;
    }

    //获取列车时刻表
    public function getSchedule($train_id){
        $result = DB::table($this->table)
            ->select('station_name', 'arrive_time', 'start_time', 'stopover_time', 'station_no')
            ->where('train_id', $train_id)
            ->get();
        return $result;
    }

    //获取地名对应的站名
    public function getStationName($place_name){
        $result = DB::table('map_place_station')
            ->select('station_name')
            ->where('place_name', $place_name)
            ->get();
        return $result;
    }

    //座位确定后更新余票信息
    public function updateTicketCount($train_id, $seat_type, $from_station_no, $to_station_no, $operate=0){
        $s = [
            '1' => 'wz_count',
            '2' => 'yz_count',
            '3' => 'yw_count',
            '4' => 'rw_count',
            '5' => 'ydz_count',
            '6' => 'edz_count',
            '7' => 'swz_count'
        ];
        $result = DB::table($this->table)
            ->where('id', $train_id)
            ->where('station_no', '>=', $from_station_no)
            ->where('station_no', '<=', $to_station_no)
            ->when($operate == 1, function ($query) use ($s, $seat_type){
                return $query->increment($s[$seat_type], 1);
            }, function ($query) use ($s, $seat_type){
                return $query->decrement($s[$seat_type], 1);
            });
        return $result;
    }

    //获取价格信息
    public function getPrice($train_id, $from_station_no, $to_station_no){
        $result = DB::table($this->table)
            ->select('seat_price')
            ->where('train_id', $train_id)
            ->where('station_no', '>=', $from_station_no)
            ->where('station_no', '<=', $to_station_no)
            ->get();
        return $result;
    }

    //模糊匹配站名
    public function getAssociateStationName($keyword){
        $result = DB::table($this->table)
            ->select('station_name')
            ->where('station_name', 'like', "%$keyword%")
            ->groupBy('station_name')
            ->get();
        return $result;
    }
}