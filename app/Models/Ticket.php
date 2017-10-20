<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/19
 * Time: 15:46
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Ticket extends Model
{
    protected $table = 'ticket';
    public $timestamps = false;
    private static $instance = null;

    public static function model() {
        if (self::$instance == null) {
            self::$instance = new Ticket();
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
}