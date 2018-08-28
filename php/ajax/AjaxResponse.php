<?php


class AjaxResponse {
    public $status, $message, $data;

    function __construct($status = null, $message = null, $data = null){
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    function jsonEncode() {
        return json_encode($this);
    }

    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function getMessage() {
        return $this->message;
    }

    function setMessage($message) {
        $this->message = $message;
    }
}

 ?>
