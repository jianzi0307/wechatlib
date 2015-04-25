<?php
/**
 * ----------------------
 * Qrcode.class.php
 * 
 * User: jian0307@icloud.com
 * Date: 2015/4/25
 * Time: 15:52
 * ----------------------
 */

namespace Lib\WechatLib;
use Lib\HttpClient;


/**
 * 获取带参数的二维码类
 *
 * @package Lib\WechatLib
 */
class Qrcode {

    /**
     * 生成二维码票据（ticket）的接口
     * @var string
     */
    static private $qrcode_ticket_url
        = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';

    /**
     * 用票据（ticket）换取二维码的接口
     * @var string
     */
    static private $show_qrcode_url
        = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s';

    /**
     * 长链接转短链接接口
     * @var string
     */
    static private $long_to_short_url
        = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=%s';

    /**
     * 创建临时二维码ticket
     * 包含验证过期并重新获取ticket
     *
     * @param int $sceneId
     * @param int $expire_seconds
     * @return bool|mixed
     * @see http://mp.weixin.qq.com/wiki/18/28fc21e7ed87bec960651f0ce873ef8a.html
     */
    static public function createQrcodeTicket($sceneId,$expire_seconds = 604800) {
        $cache_id = 'temp_qrcode_ticket_%s';
        $query_url = sprintf(self::$qrcode_ticket_url,AccessToken::getAccessToken());
        $data = self::_checkQrcodeTicket($sceneId);
        if( $data === false ) {
            $post_ary = array(
                'expire_seconds' => $expire_seconds,
                'action_name' => 'QR_SCENE',
                'action_info' => array(
                    'scene' => array(
                        'scene_id' => $sceneId
                    )
                )
            );
            $post_data = json_encode($post_ary);
            $data = HttpClient::post($query_url,$post_data);
            $data = json_decode($data,true);
            $data['atime'] = time();
            $dataJson = json_encode($data);
            S(sprintf($cache_id,$sceneId),$dataJson);
        }
        return $data;
    }

    /**
     * 检查临时二维码票据是否过期
     * @param $sceneId
     * @return bool|mixed
     */
    static private function _checkQrcodeTicket($sceneId) {
        $cache_id = 'temp_qrcode_ticket_%s';
        //获取access_token
        $data = S(sprintf($cache_id,$sceneId));
        if(!empty($data)){
            $ticket = json_decode($data, true);
            if(time() - $ticket['atime'] < $ticket['expire_seconds']-10){
                return $ticket;
            }
        }
        return false;
    }

    /**
     * 创建永久二维码ticket
     *
     * @param int|string $sceneValue 场景ID的值，可能为整型和字符串型，以sceneType为准
     * @param int $sceneType 场景ID类型： 1、32位非0整型，最大值为100000 2、字符串类型，长度限制为1到64
     * @return bool|mixed
     * Array(
     *      //获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。
     *      "ticket"=>"xxxxxxx",
     *      //二维码的有效时间，以秒为单位。最大不超过1800。
     *      "expire_seconds"=>60,
     *      //二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
     *      "url"=>"http://weixin.qq.com/q/kZgfwMTm72WWPkovabbI"
     * )
     * @see http://mp.weixin.qq.com/wiki/18/28fc21e7ed87bec960651f0ce873ef8a.html
     */
    static public function createQrcodeLimitTicket($sceneValue,$sceneType ) {
        $query_url = sprintf(self::$qrcode_ticket_url,AccessToken::getAccessToken());
        $post_ary = array();
        if( $sceneType == 1 ) {
            $post_ary['action_name'] = 'QR_LIMIT_SCENE';
            $post_ary['action_info']['scene']['scene_id'] = $sceneValue;
        } else if( $sceneType == 2 ) {
            $post_ary['action_name'] = 'QR_LIMIT_STR_SCENE';
            $post_ary['action_info']['scene']['scene_str'] = $sceneValue;
        }
        $post_data = json_encode($post_ary);
        return HttpClient::post($query_url,$post_data);
    }

    /**
     * 通过ticket换取二维码
     *
     * @param string $ticket 票据
     * @param string $filepath 文件路径，如果存在，则会保存二维码到本地
     * @return bool|mixed
     * @see http://mp.weixin.qq.com/wiki/18/28fc21e7ed87bec960651f0ce873ef8a.html
     */
    static public function getQrcodeByTicket( $ticket ,$filepath = '' ) {
        $query_url = sprintf(self::$show_qrcode_url,urlencode($ticket));
        $qrcode = HttpClient::get($query_url);
        if( !empty($filepath) ) {
            file_put_contents($filepath,$qrcode);
        }
        return $qrcode;
    }

    /**
     * 接口调用请求说明
     *
     * 主要使用场景：
     *  开发者用于生成二维码的原链接（商品、支付二维码等）太长导致扫码速度和成功率下降，
     *  将原长链接通过此接口转成短链接再生成二维码将大大提升扫码速度和成功率。
     *
     * @param string $url 待转的长链接
     * @return string JSON串
     * {"errcode":0,"errmsg":"ok","short_url":"http:\/\/w.url.cn\/s\/AvCo6Ih"}
     * @see http://mp.weixin.qq.com/wiki/10/165c9b15eddcfbd8699ac12b0bd89ae6.html
     */
    static public function toShortUrl( $url ) {
        $query_url = sprintf(self::$long_to_short_url,AccessToken::getAccessToken());
        $post_ary = array(
            'long_url' => $url,
            'action' => 'long2short'
        );
        $post_data = json_encode($post_ary);
        return HttpClient::post($query_url,$post_data);
    }
}