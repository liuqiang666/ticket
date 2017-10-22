<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/19
 * Time: 14:57
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Order extends Model
{
    protected $table = 'order';
    public $timestamps = false;
    private static $instance = null;

    public static function model() {
        if (self::$instance == null) {
            self::$instance = new Order();
        }
        return self::$instance;
    }

    public function insertRow($data) {
//        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $result = DB::table($this->table)
            ->insertGetId($data);
        return $result;
    }

    public function findRow($where, $select = '*')
    {
        $result = DB::table($this->table)
            ->select(DB::raw($select))
            ->where($where)
            ->first();
        return $result;
    }

    public function getRows($where, $select = '*'){
        $result = DB::table($this->table)
            ->select(DB::raw($select))
            ->where($where)
            ->get();
        return $result;
    }

    public function updateOrderInfo($data, $where){
        $result = DB::table($this->table)
            ->where($where)
            ->update($data);
        return $result;
    }

    public function addRelationPassengerOrder($data){
        $result = DB::table("relation_passenger_order")
            ->insertGetId($data);
        return $result;
    }

    public function getPassengerOfOrder($order_id){
        $result = DB::table("relation_passenger_order")
            ->select('passenger_id', 'price', 'passenger_type')
            ->where("order_id", $order_id)
            ->get();
        return $result;
    }

    public function getOrderInfo($user_id, $where){
        $result = DB::table($this->table)
            ->select('id as order_id', 'train_code', 'from_station_name', 'to_station_name', 'start_time', 'arrive_time',
                'seat_type', 'order_status')
            ->where($where)
            ->where('user_id', $user_id)
            ->get();
        return $result;
    }

    public function getPassengerInfoOfOrder($order_id){
        $result = DB::table('relation_passenger_order')
            ->join("passenger", 'passenger.id', 'passenger_id')
            ->select('passenger_name', 'price', 'passenger_type')
            ->where('order_id', $order_id)
            ->get();
        return $result;
    }

    public function deleteOrder($order_id){
        $result = DB::table($this->table)
            ->where('id', $order_id)
            ->delete();
        return $result;
    }
}