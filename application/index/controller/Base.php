<?php
/**
 * tpAdmin [a web admin based ThinkPHP5]
 *
 * @author    yuan1994 <tianpian0805@gmail.com>
 * @link      http://tpadmin.yuan1994.com/
 * @copyright 2016 yuan1994 all rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\File;


class Base extends Controller
{
    /*
     * 定义请求成功的函数
     */
    public static function ok($code, $message,$data="")
    {
       // var_dump($data);
        if ($data == '') {
           // var_dump(11);die;
            $result = array(
                "code" => $code,
                "message" => $message
            );
        } else {
          //  var_dump($data,22);die;
            $result = array(
                "code" => $code,
                "message" => $message,
                "data" => $data
            );
        }
       // var_dump($result);die;
        return json($result);
    }

    /*
     * 定义请求失败的函数
     */
    public static function no($code, $message)
    {
        $result = array(
            "code" => $code,
            "message" => $message

        );
        return json($result);
    }

    /**
     * 验证用户是否已经登录
     */
    public static function isNeedLogin()
    {
        $request = Request::instance();
        $userId = trim($request->param('userId'));
        if (!$userId) {
            return json(['code' => 1, 'message' => '请重新登录']);
        }
        $userInfo = Db::name('user')->where('id', $userId)->find();
        if (!$userInfo['token']) {
            return json(['code' => 1, 'message' => '请重新登录']);
        }
    }

    /**
     * 上传文件
     */
    public function upload(){
        // 获取表单上传文件
       $file =$this->request->file('file');
       // 移动到框架应用根目录/public/uploads/ 目录下
       $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');

       if($info){
           $data = $this->request->root() . '/uploads/' . $info->getSaveName();
           $insert = [
               'name'     => $data,
               'original' => $info->getInfo('name'),
               'domain'   => '',
               'type'     => $info->getInfo('type'),
               'size'     => $info->getInfo('size'),
               'mtime'    => time(),
           ];
           Db::name('File')->insert($insert);
           $data='/public/'.$data;
           return  $this->ok('1','上传成功',$data);
       }else{
            // 上传失败获取错误信息
            $error= $file->getError();
           return  $this->no('-1',$error);
        }
    }


    /**
     * 分销逻辑代码
     */
    public function fenxiao($userId)
    {
        //查看用户的信息
        $userInfo=Db::name('user')->field('id,invite_id,money,score')->where('open_id',$userId)->find();
        //查看用户的钱包

        $moneyInfo = Db::name('wallet')->field('total_add,person_rewards')->where('user_id',(int)$userInfo['id'])->find();
        //查看订单状态
       $orderInfo =Db::name('pay_info')->field('is_pay,Money,bonus')->where('is_pay',2)->where('openid',$userId)->find();
        //查找奖励的规则
        $stage_set = Db::name('stage_set')->find();
        $stageData = [
            'money' => $userInfo['money'] + $orderInfo['bonus'],
            'score' => $userInfo['score'] + $stage_set['one_score']
        ];
        //我的钱包
        $moneyData = [
            'total_add' => $moneyInfo['total_add'] + $orderInfo['bonus'],
            'person_rewards' => $moneyInfo['person_rewards'] + $orderInfo['bonus']
        ];
        //余额明细
        $logMoney=[
            'user_id'=> $userInfo['id'],
            'record_money'=>$orderInfo['bonus'],
            'type'=>0,
            'create_time'=>time()
        ];
        //用户只有在支付完成后才能获取相应的奖励
            if (!$userInfo['invite_id']) {
                //不是被邀请的，直接给他奖励 的逻辑
                //查找该好友的原有信息
                // 启动事务
                Db::startTrans();
                try {
                    Db::name('user')->where('open_id',$userId)->update($stageData);
                    //修改钱包的信息

                    Db::name('wallet')->where('user_id',(int)$userInfo['id'])->update($moneyData);
                    //添加余额明细
                    Db::name('log_money')->insert($logMoney);
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    throw new Exception($e->getMessage());
                }

            } else {

                //是被别人邀请的
                //判断该用户的上级有几级
                $user_one = Db::name('user')->field('id')->where('id',(int)$userInfo['invite_id'])->find();//下单人上一级用户的id

                //判断下单人上一级用户是否有邀请人
                $userOneInfo = Db::name('user')->field('id,money,score,invite_id')->where('id',$user_one['id'])->find();  //查找该好友的原有信息
                Db::startTrans();
                //二级用户奖励的佣金
                $orderOneInfo= $orderInfo['Money'] * $stage_set['two_stage']/100;
                try {
                    //只有两级用户,给改两级奖励
                    //下单用户
                        Db::name('user')->where('open_id',$userId)->update($stageData);
                        //修改钱包的信息
                        Db::name('wallet')->where('user_id',(int)$userInfo['id'])->update($moneyData);
                        //添加余额明细
                        Db::name('log_money')->insert($logMoney);
                    //下单上一级用户奖励
                    $stageDataOne = [
                        'money' => $userOneInfo['money'] + $orderOneInfo,
                        'score' => $userOneInfo['score'] + $stage_set['two_score']
                    ];
                    Db::name('user')->where('id',$userOneInfo['id'])->update($stageDataOne);

                    //修改钱包的信息
                    $moneyOneInfo = Db::name('wallet')->field('total_add,person_rewards')->where('user_id',$userOneInfo['id'])->find();

                    $moneyOneData = [
                        'total_add' => $moneyOneInfo['total_add'] + $orderOneInfo,
                        'person_rewards' => $moneyOneInfo['person_rewards'] + $orderOneInfo
                    ];
                   Db::name('wallet')->where('user_id',$userOneInfo['id'])->update($moneyOneData );
                    //添加余额明细
                    $logOneMoney=[
                        'user_id'=> $userOneInfo['id'],
                        'record_money'=>$orderOneInfo,
                        'type'=>0,
                        'create_time'=>time()
                    ];
                    Db::name('log_money')->insert($logOneMoney);
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    throw new Exception($e->getMessage());
                }


            }


    }
}