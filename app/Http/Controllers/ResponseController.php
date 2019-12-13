<?php

namespace App\Http\Controllers;

class ResponseController
{
    /**
     * Default API return
     *
     * @param $status
     * @param $data
     * @param string $msg
     * @param null $validation
     * @return array
     */
    public static function returnApi($status, $data, $msg = "", $validation = null){

        return [
            "status" => $status,
            "data" => $data,
            "msg" => $msg,
            "validation" => $validation
        ];
    }

    /**
     * Validation User Token
     * @return bool
     */
    public static function validationUser(){
        try {
            auth()->userOrFail();
            return true;
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return false;
        }
    }
}
