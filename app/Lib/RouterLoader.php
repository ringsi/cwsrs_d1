<?php

namespace Lib;

class RouterLoader {

    static $routerList = array();
    
    public function init($regCond, $fn) {
        self::$routerList[] = array(
            'regCond' => $regCond,
            'callback' => $fn
        );
    }

    public function register() {
        $uri = $_SERVER['REQUEST_URI'];
        $routerList = self::$routerList;
        $paths = explode('/', $uri);
        $realPath = array();
        foreach ($paths as $k => $v) {
            if ($v) {
                $realPath[] = $v;
            }
        }

        $routerFunction = null;
        foreach ($routerList as $v) {
            $preg = $v['regCond'];
            if (preg_match($preg, $uri)) {
                $routerFunction = $v['callback'];
            }
        }

        if (is_null($routerFunction)) {
            print_r("Backend Challenge");echo '<br/>';
            print_r("POST /route: Submit start point and drop-off locations");echo '<br/>';
            print_r("GET /route/<TOKEN>: Get shortest driving route");
            exit();
        }

        $routerFunction($realPath);
        exit();
    }

}
