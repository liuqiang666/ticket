<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\GenerateMd5Helper;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{

    //登陆主函数
//    public function userLogin(Request $request){
//        //判断session是否存在
//        $session = $request->session();
//        if ($request->session()->has('account')) {
//            $account = $session->all()['account'];
//            return ResponseHelper::getInstance()->jsonResponse(0, ['user' => $account]);
//        } else
//            $this->checkInformation($request);
//    }

    //检查用户信息
    public function userLogin(Request $request){
        $account = $request->input('account');
        $raw_password = $request->input('password');
//        $remember = $request->input('remember');
        //check the username
        if (trim($account) == '') {
            return ResponseHelper::getInstance()->jsonResponse(1011, null, '用户名不合法');
        }elseif (trim($raw_password) == ''){
            return ResponseHelper::getInstance()->jsonResponse(1013, null, '密码为空');
        }elseif ((User::model()->findRow(['phone_number'=>$account]))=='') {
            return ResponseHelper::getInstance()->jsonResponse(1012, null, '用户不存在');
        }
        //check the password
        $find = User::model()->findRow(['phone_number'=>$account], 'password, salt');
        $correct_password = $find->password;        //获取正确的密码
        $salt= $find->salt;                         //获取该用户的盐
        $input_password = GenerateMd5Helper::getInstance()->generateMd5($raw_password, $salt);
        if($input_password == $correct_password){
//            if ($remember == 1)
//                $request->session()->put('account', $account);
            $user = User::model()->findRow(['phone_number'=>$account], 'id, phone_number, user_name, authority');
            return ResponseHelper::getInstance()->jsonResponse(0, ['user' => $user]);
        }else{
            return ResponseHelper::getInstance()->jsonResponse(1014, null, '密码错误');
        }
    }

    // 用户登出
    public function deleteUserSession(Request $request) {
        if ($request->session()->has('account')) {
            $account = $request->session()->all()['account'];
            $request->session()->forget('account');
            return ResponseHelper::getInstance()->jsonResponse(0, $account);
        }else{
            return ResponseHelper::getInstance()->jsonResponse(0, 0);
        }
    }
}
