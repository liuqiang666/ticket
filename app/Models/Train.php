<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/16
 * Time: 23:49
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Train extends Model
{
    protected $table = 'train';
    public $timestamps = false;
    private static $instance = null;

    public static function model() {
        if (self::$instance == null) {
            self::$instance = new Train();
        }
        return self::$instance;
    }

    public function getTrainInfo($train_id){
        $result = DB::table($this->table)
            ->select('train_code', 'start_station', 'end_station')
            ->where('id', $train_id)
            ->get();
        return $result;
    }
}