
<?php
class UnprocessableException extends Exception {
    protected $statusCode;

    public function __construct($message, $statusCode = HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getResponse() {
        http_response_code($this->statusCode);
        return [
            "success" => false,
            "status_code" => $this->statusCode, 
            "message" => $this->getMessage() 
        ];
    }
}
