<?php

use Tymon\JWTAuth\Facades\JWTAuth;

class MyHelper
{

    public static function responseAPI($status, $msg, $data = [], $statuscode)
    {
        return response()->json([
            'success' => $status,
            'messages' => $msg,
            'data' => $data,
        ], $statuscode);
    }
};
