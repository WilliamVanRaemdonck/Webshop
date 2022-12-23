<?php
include_once "DbInfo.php";

//throw new Exception('Uncaught Exception occurred');

set_error_handler("handleErrors");

function handleErrors($errno, $errMsg, $errFile, $errLine) {
    $log = new ErrorLog($errno, $errMsg, $errFile, $errLine);
    $log->WriteError();
    echo "An error occurred. Please consult the error log file for more information.";
    exit();
}

class ErrorLog {
    const ERROR_FILE = "log.txt";
    private $errno;
    private $errMsg;
    private $errFile;
    private $errLine;

    public function __construct($errno=0, $errMsg="", $errFile="", $errLine="") {
        $this->errno = $errno;
        $this->errMsg = $errMsg;
        $this->errFile = $errFile;
        $this->errLine = $errLine;
    }

    public function WriteError() {
        $error = "Error logged: " . date("Y-m-d H:i:s - ");
        $error .= "[ " . $this->errno . " ]: ";
        $error .= $this->errMsg . " in file " . $this->errFile . " on line " . $this->errLine ."\n";
        
        error_log($error, 3, self::ERROR_FILE);
    }
}

// Exception -----------------------------

function handleUncaughtException($e){
    $log = new ErrorLog($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
    $log->WriteError();
    exit("An unexpected error occurred. Please contact the system administrator!");
}

set_exception_handler('handleUncaughtException');

class MyException extends Exception {
    public function HandleException() {
        $log = new ErrorLog($this->getCode(), $this->getMessage(), $this->getFile(), $this->getLine());
        $log->WriteError();
        exit($this->getMessage());
    }
}

?>