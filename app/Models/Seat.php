<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/19
 * Time: 10:10
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Seat extends Model
{
    protected $table = 'seat';
    public $timestamps = false;
    private static $instance = null;

    public static function model() {
        if (self::$instance == null) {
            self::$instance = new Seat();
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

    //获取座位信息
    public function getSeatInfo($train_id, $seat_type){
        $result = DB::table($this->table)
            ->select('id', 'carriage_number', 'seat_number', 'is_free')
            ->where('train_id', $train_id)
            ->where('seat_type', $seat_type)
            ->get();
        return $result;
    }

    //修改座位使用情况
    public function updateSeatInfo($seat_id, $data){
        $result = DB::table($this->table)
            ->where('id', $seat_id)
            ->update($data);
        return $result;
    }
}