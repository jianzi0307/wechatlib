<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/26
 * Time: 14:23
 */
namespace Lib;

class HttpClient {
    /**
     * 模拟GET
     * @param $url
     * @param array $params
     * @return bool|mixed
     */
    static public function get($url, $params = array()) {
        if (empty($url) || !is_array($params)) {
            return false;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $paramStr = '';
        if (count($params)) {
            foreach ($params as $k => $v) {
                $paramStr.=(empty($paramStr) ? '' : '&') . $k . '=' . $v;
            }
        }
        $url.=empty($paramStr) ? '' : '?' . $paramStr;
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        if ($info['http_code'] == 200)
            return $result;
        else
            return false;
    }

    /**
     * 模拟POST
     * @param $url
     * @param $params
     * @return bool|mixed
     */
    static public function post($url,$params = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        if($params) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        }
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        if ($info['http_code'] == 200)
            return $result;
        else
            return false;
    }
}
?>