<?php

namespace Controller;

use Lib\AsyncFunc;

class MapPathsController {

    public $displayLocation = 'message';
    public $pointsLocation = 'points';

    public function getRouteByToken($token) {

        $data = $this->__isTokenExist($token);

        if ($data[$this->displayLocation]) {
            $message = $data[$this->displayLocation];
            AppController::response($message);
        }
    }

    public function calculatePath($token) {
       
        $data = $this->__isTokenExist($token);
        if(isset($data[$this->displayLocation]) && $data[$this->displayLocation]['status']!=STATUS_IN_PROGRESS){
            exit();
        }
        $points = $data[$this->pointsLocation];
        $apiParam = $this->__getApiParam($points);
        $apiResult = AsyncFunc::asyncGetUrl($apiParam['url']);
        $shotestPath = $this->__getShotestPath($apiResult);
        if (sizeof($shotestPath) != 0) {
            $result = $this->__getPathResult($shotestPath, $apiParam);
            AppController::updateToken($token, $this->displayLocation, $result);
            AppController::updateLog("Calculated $token", $result);
            exit();
        }
        $result = array(
            'status' => STATUS_FAILURE,
            'error' => ROUTE_ERROR_NO_ROUTE_PROVIDED
        );
        AppController::updateToken($token, $this->displayLocation, $result);
        AppController::updateLog("Calculated $token", $result);
    }

    private function __isTokenExist($token) {

        $data = AppController::getToken($token);

        if (!$data) {
            $message = array(
                'status' => STATUS_FAILURE,
                'error' => TOKEN_NOT_EXIST
            );
            AppController::updateLog('Token Result Fail', $token);
            AppController::response($message);
        }

        return $data;
    }

    private function __getPathResult($shortestPath, $apiParam) {
        $routePoints = $apiParam['param'][$shortestPath['index']];
        $path = [$routePoints['origin']];
        foreach ($shortestPath['waypoint_order'] as $v) {
            $path[] = $routePoints['waypoints'][$v];
        }
        $path[] = $routePoints['destination'];
        $token_data = array(
            'status' => STATUS_SUCCESS,
            'path' => $path,
            'total_distance' => $shortestPath['distance'],
            'total_time' => $shortestPath['duration'],
        );
        return $token_data;
    }

    private function __getApiParam($points) {
        $origin = $points[0];
        array_shift($points);
        $destinationSet = [];
        foreach ($points as $key => $destination) {
            $wayPoints = $points;
            array_splice($wayPoints, $key, 1);
            $destinationSet['param'][$key] = array(
                'origin' => $origin,
                'destination' => $destination,
                'waypoints' => $wayPoints,
            );
            $destinationSet['url'][$key] = $this->__getGoogleMapURL($destinationSet['param'][$key]);
        }
        return $destinationSet;
    }

    private function __getGoogleMapURL($param) {
      
        $url = 'https://maps.googleapis.com/maps/api/directions/json?';
        $default = array(
            'mode' => 'driving',
            'key' => GOOGLE_API_KEY_LIVE
        );
        $data = array(
            'origin' => AppController::pt2txt($param['origin']),
            'destination' => AppController::pt2txt($param['destination']),
            'waypoints' => 'optimize:true|' . $this->__getWayPointsArray($param['waypoints']),
        );
        $result = array_merge($default, $data);
        return $url . http_build_query($result);
    }

    private function __getWayPointsArray($array) {
        $string = "";
        foreach ($array as $value) {
            if (is_array($value)) {

                $string .= rtrim(implode(',', $value), ',') . '|';
            }
        }
        return $string;
    }

    private function __getShotestPath($apiResult) {
        $tmpCost = 100000000;
        $shotestPath = [];
        foreach ($apiResult as $key => $value) {
            $phpResult = json_decode($value, 1);
            $route = $phpResult['routes'];
            if (sizeof($route) == 0) {
                continue;
            }
            $totalSpends = $this->__getTotalSpends($route[0]['legs']);
            $duration = $totalSpends['duration'];
            $distance = $totalSpends['distance'];
            $weightCost = $totalSpends['weightCost'];

            if ($weightCost < $tmpCost && $weightCost > 0) {
                $shotestPath['index'] = $key;
                $shotestPath['waypoint_order'] = $route[0]['waypoint_order'];
                $shotestPath['distance'] = $distance;
                $shotestPath['duration'] = $duration;
            }
            $tmpCost = $weightCost;
        }

        return $shotestPath;
    }

    private function __getTotalSpends($legs) {
        $result = array(
            'distance' => 0,
            'duration' => 0
        );

        foreach ($legs as $leg) {
            $result['distance'] += $leg["distance"]["value"];
            $result['duration'] += $leg["duration"]["value"];
        }
        $result['weightCost'] = ($result['duration'] * STP_DURATION_WEIGHT) + ($result['distance'] * STP_DISTANCE_WEIGHT);
        return $result;
    }

}
