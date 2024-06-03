<?php

require_once '../Database/Database.php';
require_once '../exceptions/errors.php';
require_once '../exceptions/UnprocessableException.php';

class Validation {
    public static function validateUsername($username) {
        
        if (!preg_match('/^[a-zA-Z\s]+$/', $username)) {
            throw new UnprocessableException("Username must contain only alphabetic characters.", HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }
        return ["success" => true, "message" => "Valid username"];
    }

    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UnprocessableException("Invalid email format.", HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }
        return ["success" => true, "message" => "Valid email"];
    }

    public static function validateAddress($address) {
        
        if (!preg_match('/^[a-zA-Z\s]+$/', $address)) {
            throw new UnprocessableException("Address must contain only alphabetic characters.", HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }
        return ["success" => true, "message" => "Valid address"];
    }

    public static function validatePhoneNumber($phoneNumber) {
        if (!preg_match('/^\+?[0-9]{10}$/', $phoneNumber)) {
            throw new UnprocessableException("Phone number must be exactly 10 digits.", HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }
        return ["success" => true, "message" => "Valid phone number"];
    }
}
?>
