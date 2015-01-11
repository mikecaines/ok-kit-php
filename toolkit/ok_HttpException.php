<?php
class ok_HttpException extends Exception {
    private $httpCode;

    public function getHttpCode() {
        return $this->httpCode;
    }

    public function __construct($aHttpCode) {
        parent::__construct((string)'HTTP ' . $aHttpCode);
        $this->httpCode = (int)$aHttpCode;
    }
}