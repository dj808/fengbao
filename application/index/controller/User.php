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
// 个人中心相关控制器
//-------------------------

namespace app\index\controller;


use think\Db;
use think\Request;
use think\Exception;
use app\index\model\User as userModel;



class User extends Base
{
        /**
         * 个人中心页面显示
         */
        public function user(){

            //获取用户的基本信息
            $userInfo=Db::name('user')
                ->field('id,nickname,avatar,money,score,is_autonym')
                ->where('open_id',session('useropenid'))
                ->find();

            //全部订单显示
            $waitPlayNum=Db::name('pay_info')->where('openid',session('useropenid'))->where('is_pay',1)->count(); //待付款
            $waitSendNum=Db::name('pay_info')->where('openid',session('useropenid'))->where('is_pay',2)->count(); //待配送
            $overNum=Db::name('pay_info')->where('openid',session('useropenid'))->where('is_pay',2)->count(); //已完成
            $backNum=Db::name('pay_info')->where('openid',session('useropenid'))->where('is_pay',1)->count();//已退保
            //联系电话
            $about_us= Db::name('about_us')->field('tel')->find();
            $this->view->assign('about_us',$about_us);
            $this->view->assign('waitPlayNum',$waitPlayNum);
            $this->view->assign('waitSendNum',$waitSendNum);
            $this->view->assign('overNum',$overNum);
            $this->view->assign('backNum',$backNum);
            /*if($userInfo['money']==0){
                $userInfo['money']=0;
            }*/
            $this->view->assign('userInfo',$userInfo);

            return $this->view->fetch();
        }

        /**
         * 基本信息页面
         */
        public function basic(){
            $userInfo=Db::name('user')
                ->field('id,avatar,nickname,realname,phone,qrcode')
                ->where('open_id',session('useropenid'))
                ->find();

            $userInfo['phone']=userModel::hidestr($userInfo['phone'], 3, 4);
            $userInfo['realname']=userModel::hidestr($userInfo['realname'], 0, -1); //**亮
            $this->view->assign('userInfo',$userInfo);
            return $this->view->fetch();
        }
        /**
         * 编辑用户昵称
         * @param $nickname string 用户昵称
         * @param $userId int 用户id
         * @return true |false
         */
        public function editNickName(){
            $request= Request::instance();
            $user_id =(int)$request->param('user_id');
            $nickname =trim($request->param('nickname'));
            if(!$nickname){
                return  $this->no(1,'昵称不能为空');
            }
            $isExit=Db::name('wexin')->field('id')->where('nickname',$nickname)->find();
            if($isExit){
                return  $this->no(-1,'昵称已存在，请重新编辑');
            }

            $res=Db::name('wexin')->where('id',$user_id)->setField('nickname', $nickname);
            if($res){
                return $this->ok('1','编辑成功');
            }else{
                return $this->ok('-1','系统繁忙，请稍后再试');
            }
        }
    /**
     * 编辑用户头像
     * @param $nickname string 用户昵称
     * @param $userId int 用户id
     * @return true |false
     */
    public function editAvater(){
        $request= Request::instance();
        $user_id =(int)$request->param('user_id');
        $avatar =trim($request->param('avatar'));
        /**
         * 上传照片
         */

        $res=Db::name('wexin')->where('id',$user_id)->setField('avatar', $avatar);
        if($res){
            return $this->ok('1','编辑成功');
        }else{
            return $this->ok('-1','系统繁忙，请稍后再试');
        }
    }
        /**
         * 修改密码
         * @param $userId int 用户id
         * @param $password string 密码
         * @param  $vcode  string  验证码
         */
        public function alterPWd(){
            $request= Request::instance();
            $user_id =(int)$request->param('user_id');

            //验证密码
            $password =trim($request->param('password'));
            if(!$password){
                return $this->no('-1','密码不能为空');
            }
            if(!userModel::isValidaPassword($password)){
                return $this->no('-1','请输入有效的密码');
            }

            //密码加密
            $password=userModel::getPassword($password);
            $res=Db::name('user')->where('id',$user_id)->setField('password',$password);
            if(!$res){
                return  $this->no('-1','系统繁忙，请稍后再试');
            }else{
                return $this->ok('1','修改成功');
            }

        }
        /**
         * 用户实名认证页面显示
         */
        public function getApprove(){
            //判断用户是否实名
            $is_autonym=Db::name('user')->field('is_autonym')->where('open_id',session('useropenid'))->find();
            if((int)$is_autonym['is_autonym']===2){
                $this->redirect('index/user/bindBank');
                exit;
            }

                $request= Request::instance();
                $user_id =(int)$request->param('user_id');
                //选择行业
                $industryInfo=Db::name('industry')->field('id,name')->select();
                $this->view->assign('industryInfo',$industryInfo);
                $this->view->assign('user_id',$user_id);
                return $this->view->fetch('approve');
        }

        /**
         * 用户实名认证
         */
        public function addApprove(){

            $request= Request::instance();
         //   $user_id =trim($request->param('user_id'));

            $realname =trim($request->param('username'));
            if(!$realname){
                return $this->no('-1','真实姓名不能为空');
            }
            $idcard =trim($request->param('idcard'));
            if(!$idcard){
                return $this->no('-1','身份证号不能为空');
            }

            if(FALSE===userModel::is_idcard($idcard)){
                return $this->no('-1','请输入正确的身份证号');
            }
            $photoPros =trim($request->param('idcard_pros'));
            /*if(!$photoPros){
                return $this->no('-1','身份证正面照不能为空');
            }*/
            $photoCons =trim($request->param('idcard_cons'));
            /*if(!$photoCons){
                return $this->no('-1','身份证反面照不能为空');
            }*/
            $is_agenter =$request->param('is_agenter');
           /*
            if($is_agenter!==''){
                return $this->no('-1','请选择是否保险代理人');
            }*/
            $industry_id =trim($request->param('industry_id'));
            if(!$industry_id){
                return $this->no('-1','请选择所在行业');
            }
            $data=[
                'industry_id'=>(int)$industry_id,
                'is_agenter'=>(int)$is_agenter,
                'realname'=>$realname,
                'idcard'=>$idcard,
                'idcard_pros'=>$photoPros,
                'idcard_cons'=>$photoCons,
                'is_autonym'=>2
            ];
            // 启动事务
            Db::startTrans();
            try {
                 $approveInfo=Db::name('user')->where('open_id',session('useropenid'))->update($data);
                 //给该用户奖励积分
                 $userData=Db::name('user')->field('invite_id')->where('open_id',session('useropenid'))->find();
                 $score=Db::name('stage_set')->field('invite_score')->find();

                 if($userData['invite_id']){
                     Db::name('user')->where('open_id',session('useropenid'))->setField('score',$score['invite_score']);
                 }
                if($approveInfo){
                    // 提交事务
                    Db::commit();

                    return $this->ok('1','用户认证已提交,等待审核');

                }else{
                    return $this->no('-1','系统繁忙，请稍后再试');
                }


            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                throw new Exception($e->getMessage());
            }
        }

        /**
         * 绑定银行卡
         */
        public function bindBank(){
            //判断用户是否实名
            /*$is_bindbank=Db::name('user')->field('is_bindbank,is_one')->where('open_id',session('useropenid'))->find();

            if((int)$is_bindbank['is_bindbank']===2 ||(int)$is_bindbank['is_one']===1){
                $this->redirect('index/home/index');
                exit;
            }*/

            $bankName=Db::name('bank')->field('id,name')->select();
            $this->view->assign('bankName',$bankName);
            return $this->view->fetch();
        }

        //点击跳过的逻辑
        public function tiaoguo(){
            $is_one= Db::name('user')->field('is_one')->where('open_id',session('useropenid'))->find();
            if((int)$is_one['is_one']===0){
                $res=Db::name('user')->where('open_id',session('useropenid'))->setField('is_one',1);
                if(!$res){
                    return  $this->no('-1','系统繁忙，请稍后再试');
                }else{
                    return $this->ok('1','');
                }
            }else{
                return $this->ok('1','');
            }
        }

        /**
         * 添加银行卡信息
         */
        public function addBankInfo()
        {
            $request = Request::instance();
            $user_id = trim($request->param('user_id'));

            $bank_username = trim($request->param('bank_username'));
            if (!$bank_username) {
                return $this->no('-1', '户名不能为空');
            }
            $bank_id = trim($request->param('bank_id'));
            if (!$bank_id) {
                return $this->no('-1', '开户行不能为空');
            }

            $bank_no = trim($request->param('bank_no'));
            if (!$bank_no) {
                return $this->no('-1', '银行卡号不能为空');
            }
            if(FALSE===userModel::checkBankCard($bank_no)){
                return $this->no('-1','请输入正确的银行卡号');
            }



            $bank_img = trim($request->param('bank_img'));
           /* if (!$bank_img) {
                return $this->no('-1', '银行卡照片不能为空');
            }*/
            $data = [
                'bank_username' =>$bank_username,
                'bank_no' => $bank_no,
                'bank_id' => $bank_id,
                'bank_img' => $bank_img,
                'is_bindbank'=>2
            ];
            try {
                $approveInfo = Db::name('user')->where('open_id',session('useropenid'))->update($data);
                if ($approveInfo) {
                    $returnUrl=session('tixianUrl');
                    if($returnUrl){
                        return $this->ok('2', '信息已提交,等待审核', $approveInfo);
                    }
                    return $this->ok('1', '信息已提交,等待审核', $approveInfo);

                } else {
                    return $this->no('-1', '系统繁忙，请稍后再试');
                }
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * 提现页面显示
         */
      public function tixian(){
          $infoUser = Db::name('user')->field('id,nickname,money,is_bindbank,is_one')->where('open_id',session('useropenid'))->find();
          if((int)$infoUser['is_bindbank']===1){
              session('tixianUrl','index/user/tixian');
              $this->redirect('index/user/bindBank');
              exit;
          }
          $this->view->assign('infoUser',$infoUser);
          return $this->view->fetch();
      }

       /*
        * 申请提现
        */
       public function applyMoney(){

           $request = Request::instance();
           $user_id = (int)$request->param('user_id');
           $userInfo=Db::name('user')->field('id,money')->where('id',$user_id)->find();

           $money = trim($request->param('money'));

           if (!$money) {
               return $this->no('-1', '提现金额不能为空');
           }
           if ((int)$money==0) {
               return $this->no('-1', '提现金额不能为0');
           }
           if ($money>$userInfo['money']) {
               return $this->no('-1', '提现金额不能原有的金额');
           }
           $data = [
               'money' => $money,
               'user_id'=>$user_id,
               'create_time'=>time()
           ];

           // 启动事务
          Db::startTrans();
           try {
               $applyInfo = Db::name('withdraw')->insert($data);

               Db::name('user')->where('id',$user_id)->setField('money', $userInfo['money']-$money);
               //用户原本待结算的金额（在用户注册成功后生成钱包信息）
               $moneyInfo=Db::name('wallet')->field('wait_money,block_money')->where('user_id',$user_id)->find();
               //用户钱包的钱需要修改
               Db::name('wallet')->where('user_id',$user_id)->setField('wait_money', $moneyInfo['wait_money']+$money);
               Db::name('wallet')->where('user_id',$user_id)->setField('block_money', $moneyInfo['block_money']+$money);
               //余额明细
               $logMoney=[
                   'user_id'=>  $user_id,
                   'record_money'=>$money,
                   'type'=>1,
                   'create_time'=>time()
               ];
               Db::name('log_money')->insert($logMoney);
               if ($applyInfo) {
                   // 提交事务
                   Db::commit();
                   return $this->ok('1', '提现信息已提交,等待审核');
               } else {
                   return $this->no('-1', '系统繁忙，请稍后再试');
               }
           } catch (\Exception $e) {
               // 回滚事务
               Db::rollback();
               throw new Exception($e->getMessage());

           }

       }
    /**
     * 兑换页面显示
     */
    public function change(){
        $infoUser = Db::name('user')->field('id,nickname,score')->where('open_id',session('useropenid'))->find();
        $this->view->assign('infoUser',$infoUser);
        return $this->view->fetch();
    }

    /*
     * 申请兑换
     */
    public function applyScore(){

        $request = Request::instance();
        $user_id = (int)$request->param('user_id');
        $userInfo=Db::name('user')->field('id,score,money')->where('id',$user_id)->find();

        $score = trim($request->param('score'));

        if (!$score) {
            return $this->no('-1', '兑换积分不能为空');
        }
        if ($score>$userInfo['score']) {
            return $this->no('-1', '兑换积分不能原有的积分');
        }
        if ((int)$score==0) {
            return $this->no('-1', '兑换积分不能为0');
        }
        /*$data = [
            'score' => $score,
            'user_id'=>$user_id,
            'create_time'=>time()
        ];*/
        // 启动事务
        Db::startTrans();
        try {
          //  $applyInfo = Db::name('withdraw')->insert($data);
            $applyInfo= Db::name('user')->where('id',$user_id)->setField('score', $userInfo['score']-$score);
            //兑换逻辑
             $exchange=Db::name('stage_set')->field('score_exchange')->find();
             //兑换后的金额
            $changeMoney=1/(int)$exchange['score_exchange']*$score;
             //余额改变
            Db::name('user')->where('id',$user_id)->setField('money', $userInfo['money']+$changeMoney);
            //余额明细
            $logMoney=[
                'user_id'=>  $user_id,
                'record_money'=>$changeMoney,
                'type'=>0,
                'create_time'=>time()
            ];
            Db::name('log_money')->insert($logMoney);

            // 提交事务
           Db::commit();
            if ($applyInfo) {
                return $this->ok('1', '提现信息已提交,等待审核');
            } else {
                return $this->no('-1', '系统繁忙，请稍后再试');
            }
        } catch (\Exception $e) {
            // 回滚事务
           Db::rollback();
            throw new Exception($e->getMessage());

        }

    }



}

