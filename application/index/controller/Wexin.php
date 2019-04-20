<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/14
 * Time: 9:53
 */
namespace app\index\controller;



use think\Controller;
use think\Config;



/**
 * 前端首页控制器
 * Class Index
 * @package app\index\controller
 */
class Wexin extends Controller
{


   

    // 用于请求微信接口获取数据
    public function getOpenid($code)
    {
        //获取微信配置参数
		 
        $wxConfig = Config::get('wechat');
        $appId = $wxConfig['AppId'];
        $appSecret = $wxConfig['AppSecret'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appId&secret=$appSecret&code=$code&grant_type=authorization_code";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);

        curl_close($curl);
        file_put_contents("log_openid.log", $tmpInfo, FILE_APPEND);
        $openid = json_decode(htmlspecialchars_decode($tmpInfo), true);
        if (isset($openid['openid'])) {
            session('opennewid', $openid['openid']);
            session('newaccess_token', $openid['access_token']);
            return $openid;
        } else {
            $newopenid['openid'] = session('opennewid');
            $newopenid['access_token'] = session('newaccess_token');
            return $newopenid;
        }
    }

    //获取access_token
    public function getAccesstoken()
    {
        //获取微信配置参数
        $wxConfig = Config::get('wechat');
        $appId = $wxConfig['AppId'];
        $appSecret = $wxConfig['AppSecret'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, false);
        $result = curl_exec($ch);
        curl_close($ch);
        file_put_contents("log_accesstoken.txt", $result);
        $access_token = json_decode(htmlspecialchars_decode($result), true);

        return $access_token['access_token'];


    }



    //获取微信用户的信息
    public function getWxUserInfo($openid, $access_token)
    {
        $userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $userinfo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, false);
        $result = curl_exec($ch);
        curl_close($ch);
        file_put_contents("log_wx.txt", $result);
        $info = json_decode(htmlspecialchars_decode($result), true);

        return $info;
    }

}