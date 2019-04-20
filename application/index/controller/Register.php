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


use app\index\model\User as userModel;
use think\Db;
use think\Request;
use think\Config;
use Aliyun\Core\Config as AliyunConfig;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;


class Register extends Base
{

    public function index(){
       
        return  $this->view->fetch();
    }
    /**
     * 用户注册
     * @param $mobile string  手机号码
     * @param $possword string  密码
     * @param $vcode string  验证码
     * @return true |false
     */
    public function register(){
        $request= Request::instance();
       
       //验证手机号码
        $phone =trim($request->param('phone'));
        if(!$phone){
            return $this->no('-1','手机号码不能为空');
        }

        if( false===userModel::isValidaMobile($phone)){
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
        //确定密码
        $rpassword=trim($request->param('rpassword'));
        if(!$rpassword){
            return $this->no('-1','确定密码不能为空');
        }
        //判断确定密码是否与设定密码一致
        if($rpassword!==$password){
            return $this->no('-1','两次设定的密码不一致');
        }


        //验证手机号码是否已注册
        $userInfo=Db::name('user')->where('phone',$phone)->find();
        if($userInfo){
            return $this->no('-1','该手机号码已经注册,请直接登录');
        }

        //验证手机验证码是否为空
        $vCode=trim($request->param('vCode'));
        if(!$vCode){
            return  $this->no('-1','验证码不能为空');
        }
        //验证手机验证码是否正确
        $vcode=Db::name('sms')->field('code')->where('phone',$phone)->order('id desc')->find();
        if($vCode!=$vcode['code']) {
            return $this->no('-1', '请输入正确的验证码');

        }
        //手机密码加密
        $password=userModel::getPassword($password);
        $data=[
            'phone'=>$phone,
            'password'=>$password,
            'create_time'=>time() 
        ];
        // 启动事务
        Db::startTrans();
        try {
            $result = Db::name('user')
                ->where('open_id',session('useropenid'))
                ->update($data);
            //生成用户的钱包信息
            $user_id=Db::name('user')->field('id')->where('open_id',session('useropenid'))->find();
            $wallet=[
                'user_id'=>$user_id['id']
            ];
            Db::name('wallet')->insert($wallet);
           //生成用户的钱包信息结束
            if(!$result){
                return  $this->no('-1','系统繁忙，请稍后再试');
            }else{
              Db::commit();
                return  $this->ok('1','注册成功');
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw new Exception($e->getMessage());

        }
    }


    //发送手机短信
    public function sendSms()
    {
        $phone=trim($this->request->param('phone'));

        if(!$phone){
            return  $this->no('-1','手机号码不能为空');
        }
        if( false===userModel::isValidaMobile($phone)){
            return $this->no('-1','请输入正确的手机号码');
        }
        //code就是六位随机码作为验证码
        //引进阿里的配置文件


        vendor('alidayu.vendor.autoload');
        // 加载区域结点配置
        AliyunConfig::load();				//加载区域结点配置

        $smsConfig = Config::get('sendSms');

        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($smsConfig['ALI_SMS']['REGION'], $smsConfig['cfg_smssid'],$smsConfig['cfg_smstoken']);

        // 增加服务结点
        DefaultProfile::addEndpoint($smsConfig['ALI_SMS']['END_POINT_NAME'], $smsConfig['ALI_SMS']['REGION'], $smsConfig['ALI_SMS']['PRODUCT'], $smsConfig['ALI_SMS']['DOMAIN']);
        // 初始化AcsClient用于发起请求

        $acsClient = new DefaultAcsClient($profile);

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($phone);

        // 必填，设置签名名称
        $request->setSignName($smsConfig['cfg_smsname']);
        // 必填，设置模板CODE
        $request->setTemplateCode('SMS_122293648');


        $code=str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $params = array(
            'code' =>$code
        );

        $d['code']=$code;
        $d['phone']=$phone;
        $d['create_time']=time();

        Db::name('sms')->insert($d);
        // 可选，设置模板参数
        $request->setTemplateParam(json_encode($params));
        // 可选，设置流水号

        // 发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);

        // 打印请求结果
        if($acsResponse){
            return  $this->ok('1','手机验证码发送成功');
        }else{
            return  $this->no('-1','系统繁忙，请重试');
        }
    }


    /**
     * 查看用户注册登录协议
     */
     public function getAgreement(){
         $aboutInfo = Db::name('agreement')->find();
         $this->view->assign('aboutInfo',$aboutInfo);
         return  $this->view->fetch('agreement');
     }

}