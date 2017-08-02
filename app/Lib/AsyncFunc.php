<?php

namespace Lib;

class AsyncFunc {

    public function asynToken($token) {
        $ch = curl_init();
        $url = "http://" . $_SERVER['HTTP_HOST'] . "/calculatePath/" . $token;
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return don't print
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_COOKIE, $strCookie);


        curl_exec($ch);
        curl_close($ch);
    }

    public function asyncGetUrl($url_array, $wait_usec = 0) {
        if (!is_array($url_array))
            return false;

        $wait_usec = intval($wait_usec);

        $data = array();
        $handle = array();
        $running = 0;

        $mh = curl_multi_init(); // multi curl handler

        $i = 0;
        foreach ($url_array as $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return don't print
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
            curl_setopt($ch, CURLOPT_MAXREDIRS, 7);
            curl_multi_add_handle($mh, $ch);
            $handle[$i++] = $ch;
        }

        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);


        foreach ($handle as $i => $ch) {
            $content = curl_multi_getcontent($ch);
            $data[$i] = (curl_errno($ch) == 0) ? $content : false;
        }

        /* remove handle */
        foreach ($handle as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }

        curl_multi_close($mh);
        return $data;
    }

}
