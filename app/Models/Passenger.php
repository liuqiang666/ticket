<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/19
 * Time: 21:00
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Passenger extends Model
{
    protected $table = 'passenger';
    public $timestamps = false;
    private static $instance = null;

    public static function model() {
        if (self::$instance == null) {
            self::$instance = new Passenger();
        }
        return self::$instance;
    }

    public function insertRow($data) {
//        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $result = DB::table($this->table)
            ->insertGetId($data);
        return $result;
    }

    public function findPassenger($where, $select = '*')
    {
        $result = DB::table($this->table)
            ->select(DB::raw($select))
            ->where($where)
            ->get();
        return $result;
    }

    public function updatePassengerInfo($where, $data){
        $result = DB::table($this->table)
            ->where($where)
            ->update($data);
        return $result;
    }

    public function deletePassenger($passenger_id){
        $result = DB::table($this->table)
            ->where('id', $passenger_id)
            ->delete();
        return $result;
    }
}