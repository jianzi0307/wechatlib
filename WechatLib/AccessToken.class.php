<?php
/**
 * ----------------------
 * AccessToken.class.php
 * 
 * User: jian0307@icloud.com
 * Date: 2015/4/25
 * Time: 14:54
 * ----------------------
 */

namespace Lib\WechatLib;

use Lib\HttpClient;
use Think\Exception;


/**
 * 微信开放平台获取基础Access_token类
 * 包含验证过期并重新获取
 *
 * @package Lib\WechatLib
 */
class AccessToken {

    /**
     * 缓存access_token的key
     * @var string
     */
    static private $cache_id = "base_access_token";

    /**
     * 获取基础access_token的url
     * @var string
     */
    static private $access_token_url
        = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';

    /**
     * 获取基础access_token
     * @return string
     */
    static public function getAccessToken() {
        $accessToken = self::_checkAccessToken();
        if($accessToken === false){
            $accessToken = self::_getAccessToken();
        }
        return $accessToken['access_token'];
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    static private function _getAccessToken() {
        $url = sprintf(self::$access_token_url,APPID,APPSECRET);
        $accessToken = HttpClient::get($url);
        $accessToken = json_decode($accessToken,true);
        if(!isset($accessToken['access_token'])){
            throw new Exception("获取ACCESS_TOKEN失败");
        }
        //缓存时间，用来判断access_token是否过期
        $accessToken['atime'] = time();
        $accessTokenJson = json_encode($accessToken);
        //缓存基础access_token
        S(self::$cache_id,$accessTokenJson);
        return $accessToken;
    }

    /**
     * 验证access_token是否过期，过期重新获取
     * @return bool
     */
    static private function _checkAccessToken() {
        //获取access_token
        $data = S(self::$cache_id);
        if(!empty($data)){
            $accessToken = json_decode($data, true);
            if(time() - $accessToken['atime'] < $accessToken['expires_in']-10){
                return $accessToken;
            }
            S(self::$cache_id,null);
        }
        return false;
    }
}