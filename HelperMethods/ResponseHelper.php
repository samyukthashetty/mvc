<?php

class ResponseHelper {
    
    public static function success($message, $data = null, $status_code = HttpStatusCodes::HTTP_OK) {
        return [
            "success" => true,
            "message" => $message,
            "data" => $data,
            "status_code" => $status_code
        ];
    }

    public static function error($message, $status_code) {
        return [
            "success" => false,
            "message" => $message,
            "status_code" => $status_code
        ];
    }
}
