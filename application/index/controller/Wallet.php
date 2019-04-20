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
// 其他相关控制器
//-------------------------

namespace app\index\controller;

use think\Db;
use think\Request;

class Wallet extends Base
{
    /**
     * 我的钱包明细简介
     */
   public  function  getWalletInfo(){
       if(!session('useropenid')){
           $this->redirect('index/login/index');
       }
       $request=Request::instance();
       $user_id=(int)$request->param('user_id');
       $walletInfo = Db::name('wallet')
           ->alias('a')
           ->field('a.*,b.money,score')
           ->join('__USER__ b','a.user_id=b.id')
           ->where('a.user_id',$user_id)
           ->find();
       $this->view->assign('walletInfo',$walletInfo);
       $this->view->assign('user_id',$user_id);
       return $this->view->fetch();

   }
    /**
     * 我的余额明细
     */
    public  function  getMoneyLog(){
        $request=Request::instance();
        $user_id=(int)$request->param('user_id');
        $moneyLog = Db::name('log_money')
            ->where('user_id',$user_id)
            ->select();
        $this->view->assign('moneyLog',$moneyLog );
        $this->view->assign('user_id',$user_id);
        return $this->view->fetch();

    }
    /**
     * 我的余额明细
     */
    public  function  getExpendLog(){
        $request=Request::instance();
        $user_id=(int)$request->param('user_id');
        $moneyLog = Db::name('log_money')
            ->where('user_id',$user_id)
            ->where('type',0)
            ->select();
        $this->view->assign('moneyLog',$moneyLog );
        $this->view->assign('user_id',$user_id);
        return $this->view->fetch();

    }
    /**
     * 我的余额明细
     */
    public  function  getSendLog(){
        $request=Request::instance();
        $user_id=(int)$request->param('user_id');
        $moneyLog = Db::name('log_money')
            ->where('user_id',$user_id)
            ->where('type',1)
            ->select();
        $this->view->assign('moneyLog',$moneyLog );
        $this->view->assign('user_id',$user_id);
        return $this->view->fetch();

    }
   /* public function getMoneyLogAjax(){
        $request=Request::instance();
        $user_id=(int)$request->param('user_id');
        $type=(int)$request->param('type');
        $moneyLog = Db::name('log_money')
            ->where('user_id',$user_id)
            ->where('type',$type)
            ->select();
      //  $data=json_encode($moneyLog);
        if($moneyLog){
            return $this->ok('1', '请求成功',$moneyLog);
        }else{
            return $this->no('-1', '暂无数据');
        }

    }*/
    /**
     * 提现记录查询
     */
    public function getWithdrawLog(){
        $request=Request::instance();
        $user_id=(int)$request->param('user_id');

        $withdraw=Db::name('withdraw')->where('user_id',$user_id)->order('create_time desc')->select();

        $this->view->assign('withdraw',$withdraw);
        return $this->view->fetch();
    }

}