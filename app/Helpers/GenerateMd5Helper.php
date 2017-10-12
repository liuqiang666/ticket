<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 2017/7/10
 * Time: 11:18
 */

namespace App\Helpers;

class GenerateMd5Helper
{
    private static $instance = null;

    public static function getInstance(){
        if (self::$instance == null) {
            self::$instance = new GenerateMd5Helper();
        }
        return self::$instance;
    }

    public function generateSalt($length=6)
    {
        $salt = '';
        $chars = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789';
        $len_chars = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i ++)
        {
            $ran = rand(0, $len_chars);
            $salt .= $chars[$ran];
        }
        return $salt;
    }

    public function generateMd5($password, $salt)
    {
        $password .= $salt;
        return md5($password);
    }

    public function checkPassword($password, $correctPassword, $salt)
    {
        $inputPassword = $this->generateMd5($password, $salt);
        if($inputPassword == $correctPassword)
            return true;
        return false;
    }
}