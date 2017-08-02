<?php

namespace Controller;

class AppController {

     public function updateLog($case, $data) {
        $fp = fopen(APP . DS . "debug" . DS . "debugLog.txt", "a");
        $a = print_r($case, true);
        $b = print_r($data, true);
        fwrite($fp, "Start " . date("Y-m-d H:i:s") . " \n");
        fwrite($fp, $a . " \r\n");
        fwrite($fp, $b . " \r\n");
        fclose($fp);
    }

    public function updateToken($token, $location, $data = array()) {
        session_start();
        $_SESSION[$token][$location] = $data;
        session_write_close();
    }

    public function getToken($token) {

        session_start();
        $result = null;
        if ($_SESSION[$token]) {
            $result = $_SESSION[$token];
        }
        session_write_close();
        return $result;
    }

    public function response($output) {
        header('Content-Type: application/json');
        echo json_encode($output);
        exit();
    }

    public function pt2txt($pt) {
        return implode(',', $pt);
    }



}
