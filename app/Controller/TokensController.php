<?php

namespace Controller;

use Lib\ValidateLatLog;
use Lib\AsyncFunc;

class TokensController {

    public $displayLocation = 'message';
    public $pointsLocation = 'points';

    public function init($param) {

        if (sizeof($param) == 0) {
            AppController::response(array("error" => TOKEN_ERROR_NO_INPUT));
        }

        $ftParam = current(array_keys($param));
        $points = $param[$ftParam];
        if ($this->isJson($points)) {
            $points = json_decode($points, true);
        }
        $validation = $this->__validatePoints($points);

        if ($validation !== true) {
            AppController::response(array("error" => $validation));
        }

        $token = $this->__genToken();
        $result = array(
            'status' => STATUS_IN_PROGRESS
        );

        AppController::updateToken($token, $this->pointsLocation, $points);
        AppController::updateToken($token, $this->displayLocation, $result);
        AppController::updateLog('Get Token', $token);
        AsyncFunc::asynToken($token);
        AppController::response(array("token" => $token));
    }

    private function __genToken() {
        $token = bin2hex(random_bytes(4)) . "-"
                . bin2hex(random_bytes(2)) . "-"
                . bin2hex(random_bytes(2)) . "-"
                . bin2hex(random_bytes(2)) . "-"
                . bin2hex(random_bytes(6));
        return $token;
    }

    private function __validatePoints($param) {
        if (sizeof($param) > 10) {
            return TOKEN_ERROR_WAYPOINT_MAX;
        }
        if (sizeof($param) < 2) {
            return TOKEN_ERROR_WAYPOINT_MIN;
        }
        foreach ($param as $key => $latlong) {
            if (!ValidateLatLog::validateLatLong($latlong)) {
                return TOKEN_ERROR_WAYPOINT_FORMAT_ERROR;
            }
        }
        return true;
    }

    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

}
