<?php

/**
 * Created by PhpStorm.
 * User: wei
 * Date: 2017/7/4
 * Time: 20:06
 */

namespace App\Helpers;

use stdClass;

class ResponseHelper {

    private static $instance = null;

    public static function getInstance(){
        if (self::$instance == null) {
            self::$instance = new ResponseHelper();
        }
        return self::$instance;
    }

    public function jsonResponse($errorCode, $result = array(), $msg = '', $extraInfo = array()) {
        $response = response()->json([
            'errorCode' => $errorCode,
            'data' => $result,
            'msg' => $msg == '' ? config("errorCode.$errorCode", '') : $msg,
            'extraInfo' => $extraInfo,
        ], JSON_UNESCAPED_UNICODE);
        return $response;
    }


    /**
     * 数组 转 对象
     *
     * @param array $arr 数组
     * @return object
     */
    public function arrayToObject($arr) {
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)$this->arrayToObject($v);
            }
        }
        return (object)$arr;
    }

    //只将数组的第一层转换为遍历转换为对象，
    public function simpleArrayToObject($array){
        if (is_array($array)) {
            $obj = new StdClass();
            foreach ($array as $key => $val){
                $obj->$key = $val;
            }
        }
        else { $obj = $array; }
        return $obj;
    }

    //数组转化为<|>间隔的特殊格式
    public function array2noodle($array, $old_noodle='<|>'){
        $noodle = $old_noodle;
        foreach ($array as $element){
            $noodle .= $element . '<|>';
        }
        return $noodle;
    }

    //<|>间隔的特殊格式转化为数组
    public function noodle2array($noodle){
        $result= explode('<|>', $noodle);
        array_splice($result, 0, 1);
        array_splice($result, -1, 1);

        return $result;
    }


}
