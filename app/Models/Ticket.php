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

    public function getRows($where, $select = '*'){
        $result = DB::table($this->table)
            ->select(DB::raw($select))
            ->where($where)
            ->get();
        return $result;
    }

    public function deleteTicket($ticket_id){
        $result = DB::table($this->table)
            ->where('id', $ticket_id)
            ->delete();
        return $result;
    }

    public function updateTicketInfo($data, $where){
        $result = DB::table($this->table)
            ->where($where)
            ->update($data);
        return $result;
    }

    public function getTicketInfo($where){
        $result = DB::table($this->table)
            ->join('passenger', 'passenger.id', 'passenger_id')
            ->join('seat', 'seat.id', 'seat_id')
            ->select('ticket.id as ticket_id', 'passenger_name', 'credentials_number', 'train_code', 'carriage_number', 'seat_number',
                'from_station', 'seat_type', 'to_station', 'ticket_start_time', 'ticket_type', 'ticket_price', 'ticket_status')
            ->where($where)
            ->get();
        return $result;
    }
}