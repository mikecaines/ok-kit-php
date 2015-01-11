<?php
require_once 'ok_UserFriendlyExceptionInterface.php';

class ok_UserFriendlyException extends Exception implements ok_UserFriendlyExceptionInterface {
    public function getUserFriendlyMessage() {
        return $this->getMessage();
    }
}