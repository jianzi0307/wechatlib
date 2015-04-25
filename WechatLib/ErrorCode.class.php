<?php
/**
 * ----------------------
 * ErrorConst.class.php
 * 
 * User: jian0307@icloud.com
 * Date: 2015/4/25
 * Time: 15:01
 * ----------------------
 */

namespace Lib\WechatLib;


class ErrorCode {

    /** ------ 系统错误 ----- */

    //系统错误
    const ERROR_SYSTEM = 101;

    //图文消息的项数超过10
    const ERROR_NEWS_ITEM_COUNT_MORE_TEN = 102;

    //菜单跳转失败，请重试
    const ERROR_MENU_CLICK = 103;

    /** ------ 用户级错误 ----- */

    //输入有误，请重新输入
    const ERROR_INPUT_ERROR = 1001;

    //收到了未知类型的消息
    const ERROR_UNKNOW_TYPE = 1002;

    //验证码错误
    const ERROR_CAPTCHA_ERROR = 1003;

    //必填项未填写全
    const ERROR_REQUIRED_FIELDS = 1004;

    //远程服务器未响应
    const ERROR_REMOTE_SERVER_NOT_RESPOND = 1201;

    //获取ACCESS_TOKEN失败
    const ERROR_GET_ACCESS_TOKEN = 1202;

    //菜单不存在
    const ERROR_MENU_NOT_EXISTS = 1401;

    //未绑定微信时错误
    const ERROR_NO_BINDING_TEXT = '对不起，您尚未绑定微信!';
}