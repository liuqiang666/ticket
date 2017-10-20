<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2017/10/19
 * Time: 21:06
 */

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\Passenger;


class PassengerController extends Controller
{
    public function addPassenger(Request $request){
        $data = $request->all();
        $result = Passenger::model()->insertRow($data);
        if($result)
            return ResponseHelper::getInstance()->jsonResponse(0, [$result], 'add success');
        return ResponseHelper::getInstance()->jsonResponse(1101, $data);
    }

    public function getPassengerOfUser(Request $request){
        $user_id = $request['user_id'];
        $passengers = Passenger::model()->findPassenger(['user_id' => $user_id], 'passenger_name, 
                        credentials_number, credentials_type, phone_number, passenger_type');
        return ResponseHelper::getInstance()->jsonResponse(0, $passengers, 'passenger');
    }

    public function changePassengerInfo(Request $request){
        $passenger_id = $request['passenger_id'];
        $data = $request->except('passenger_id');
        $result = Passenger::model()->updatePassengerInfo(['id' => $passenger_id], $data);
        if($result)
            return ResponseHelper::getInstance()->jsonResponse(0, [$result], 'update success');
        return ResponseHelper::getInstance()->jsonResponse(1100, $data, 'update error');
    }

    public function deletePassenger(Request $request){
        $passenger_id = $request['passenger_id'];
        $result = Passenger::model()->deletePassenger($passenger_id);
        if($result)
            return ResponseHelper::getInstance()->jsonResponse(0, [$result], 'delete success');
        return ResponseHelper::getInstance()->jsonResponse(1100, $passenger_id, 'delete error');
    }


}