<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\GenerateMd5Helper;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function userRegister(Request $request) {
        $data = $request->all();
        $responseHelper = ResponseHelper::getInstance();
        $generateHelper = GenerateMd5Helper::getInstance();

        if(empty($data['user_name']) || empty($data['password']) || empty($data['phone_number']))
            return $responseHelper->jsonResponse(1001, [], '用户名或密码不能为空');

        $old_user_id = User::model()->findRow(['phone_number' => $data['phone_number']], 'id');
        if($old_user_id)
            return $responseHelper->jsonResponse(1002, [], '用户已存在');

        $password = $request->input('password');
        $salt = $generateHelper->generateSalt();
        $pass_md5 = $generateHelper->generateMd5($password, $salt);
        $data['password'] = $pass_md5;
        $data['salt'] = $salt;
        $data['authority'] = 0;
        $new_user_id = User::model()->insertRow($data);
        if ($new_user_id) {
            return $responseHelper->jsonResponse(0, ['new_user_id' => $new_user_id]);
        } else {
            // TODO: another error code
            return $responseHelper->jsonResponse(1101, [],'插入数据库失败');
        }
    }
}
