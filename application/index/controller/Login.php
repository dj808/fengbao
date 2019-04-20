<?php
/**
 * tpAdmin [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://tpadmin.yuan1994.com/
 * @copyright 2016 yuan1994 all rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

//------------------------
// 默认控制器
//-------------------------

namespace app\index\controller;

use app\index\model\User as UserModel;
use think\Db;
use think\Request;
use think\Config;



class Login extends Base
{

    /**
     * 授权获取微信的基本信息
     */

    public function index(){
        $wexin=new Wexin();
		$invite_id=$this->request->get('invite_id');
        if ($this->request->param('code')==''){//没有code，去微信接口获取code码

            $this->getcodess($invite_id);
            file_put_contents("log322.log",2);
        } else {//获取code后跳转回来到这里了
            $code=$this->request->param('code');
		   //邀请人
		    $inviteId=$this->request->get('inviteId');
		    if(!$inviteId){
                   $inviteId=0;
            }
            $openid = $wexin->getOpenid($code);
            $access_token=$wexin->getAccesstoken();
            $wxUserInfo=$wexin->getWxUserInfo(session('opennewid'),session('newaccess_token'));
            $user_openid = $wxUserInfo['openid'];
            session('useropenid',$user_openid);
            // 判断用户是否存在
            $data_user = Db::name('user')->where('open_id',$user_openid)->find();
            if(empty($data_user)){
                $data['open_id'] = $user_openid;
                $data['nickname']=$wxUserInfo['nickname'];
                $data['avatar']=$wxUserInfo['headimgurl'];
				$data['invite_id']=intval($inviteId);
                $data['create_time']=time();
                Db::name('user')->insert($data);
            }else{
                $data['nickname']=$wxUserInfo['nickname'];
                $data['avatar']=$wxUserInfo['headimgurl'];
                $data['update_time']=time();
                Db::name('user')->where('open_id',$user_openid)->update($data);
                
            }
            $res=Db::name('user')->field('phone,is_autonym,is_bindbank,is_one')->where('open_id',session('useropenid'))->find();
            if($res['phone']&&$res['is_autonym']=='2'&&(int)$res['is_one']!==1){
                $this->redirect('index/user/bindBank');
                exit;
            }
            if($res['phone']&&$res['is_autonym']=='2'&&(int)$res['is_one']===1){
                $this->redirect('index/home/index');
                exit;
            }
        }
        return $this->view->fetch();
    }
	
	 //授权反馈
    public function getcodess($invite_id)
    {
        $wxConfig = Config::get('wechat');
        $url=$wxConfig['WebUrl']."?inviteId=$invite_id";
        //获取微信配置参数
        $appId=$wxConfig['AppId'];
        $url = rawurlencode($url);

        $s = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appId&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        header("Location:" . $s . "");
        exit;
    }
    /**
     * 用户登录
     * @param $mobile string  手机号码
     * @param $possword string  密码
     */

    public function login(){
        $request= Request::instance();
        //验证手机号码
        $phone =$request->param('phone');
       
        if(!$phone){
            return $this->no('-1','手机号码不能为空');
        }
        if(FALSE===userModel::isValidaMobile($phone)){
            return $this->no('-1','请输入正确的手机号码');
        }
        //验证手机密码
        $password=trim($request->param('password'));
     
        if(!$password){
            return $this->no('-1','密码不能为空');
        }
        if(!userModel::isValidaPassword($password)){
            return $this->no('-1','请输入有效的密码');
        }
        $info=Db::name('user')->where('phone',$phone)->find();
		
        if(!$info){
            return $this->no('-1','用户未注册');
        }else{
            //判断密码是否一致
            if(md5($password)!==$info['password']){
                return $this->no('-1','密码不正确');
            }else{

                return $this->ok('1','登录成功');
            }
        }

    }

    /**
     * 重置密码页面显示
     * @return string
     * @throws \think\Exception
     */
    public function pwd(){
        return $this->view->fetch();
    }

    /**
     * 忘记密码
     * @param $password string 密码
     *
     */
    public function resetPwd()
    {
            $request = Request::instance();

            //验证手机号码
            $phone = trim($request->param('phone'));
            if (!$phone) {
                return $this->no('-1', '手机号码不能为空');
            }
            if (false === userModel::isValidaMobile($phone)) {
                return $this->no('-1', '请输入正确的手机号码');
            }
            //验证手机密码
            $vcode = trim($request->param('vcode'));
            if (!$vcode) {
                return $this->no('-1', '验证码不能为空');
            }

            $vCode = Db::name('sms')->field('code')->where('phone', $phone)->order('id desc')->find();
            if ($vcode != $vCode['code']) {
                return $this->no('-1', '请输入正确的验证码');
            }
            //验证手机密码
            $password = trim($request->param('password'));
            if (!$password) {
                return $this->no('-1', '密码不能为空');
            }

            if (!userModel::isValidaPassword($password)) {
                return $this->no('-1', '请输入有效的密码');
            }
            $info=Db::name('user')->where('phone',$phone)->find();
            if(!$info){
                return $this->no('-1', '用户未注册');
            }

            //手机密码加密
            $password = userModel::getPassword($password);
            $res = Db::name('user')->where('phone', $phone)->setField('password', $password);
            if (!$res) {
                return $this->no(-1, '系统繁忙，请稍后再试');
            }else{
                return $this->ok(1, '修改成功');
            }
      }

    /**
     * 重置密码页面显示
     * @return string
     * @throws \think\Exception
     */
    public function code(){
        return $this->view->fetch();
    }

    /**
     * 验证码登录
     */
    public function codeLogin()
    {
        $request = Request::instance();

        //验证手机号码
        $phone = trim($request->param('phone'));
        if (!$phone) {
            return $this->no('-1', '手机号码不能为空');
        }
        if (false === userModel::isValidaMobile($phone)) {
            return $this->no('-1', '请输入正确的手机号码');
        }
        //验证手机密码
        $vcode = trim($request->param('vcode'));
        if (!$vcode) {
            return $this->no('-1', '验证码不能为空');
        }

        $vCode = Db::name('sms')->field('code')->where('phone', $phone)->order('id desc')->find();
        if ($vcode != $vCode['code']) {
            return $this->no('-1', '请输入正确的验证码');
        }
        $info=Db::name('user')->where('phone',$phone)->find();
        if(!$info){
            return $this->no('-1','用户未注册');
        }else{
            return $this->ok('1','登录成功');
        }
    }



    /**
     * 用户退出
     * @param $userId int 用户id
     * @return true |false
     */
    public function logout(){
       //直接跳登录页面就可以了
    }

}