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
// 全部订单
//-------------------------

namespace app\index\controller;

use think\Db;
use think\Request;
use app\index\controller\Enquiry;

class Order extends Base
{

    /**
     * 所有订单列表展示
     */
    public  function  orderAll(){
        if(!session('useropenid')){
            $this->redirect('index/login/index');
        }
        return $this->view->fetch();
    }

   public  function  orderAllShow(){

       $request= Request::instance();
       $data =$request->param();
       $orderAll=Db::name('pay_info')
           ->alias('a')
           ->field('a.*,b.frame_no')
           ->join('__CAR__ b','a.LicenseNo=b.car_no')
           ->where('openid',session('useropenid'))
           ->order('id desc')
           ->limit((int)$data['page'],(int)$data['pagesize'])
           ->select();
       if($orderAll){
           return $this->ok('1','查询成功',$orderAll);
       }else{
           return $this->no('-1','查询失败');

       }

   }

    //待支付订单
    public  function  waitPlay(){
        return $this->view->fetch();
    }
    public  function  waitPlayShow(){
        $request= Request::instance();
        $data =$request->param();
        $orderAll=Db::name('pay_info')
            ->alias('a')
            ->field('a.*,b.frame_no')
            ->join('__CAR__ b','a.LicenseNo=b.car_no')
            ->where('openid',session('useropenid'))
            ->where('is_pay',1)
            ->order('id desc')
            ->limit((int)$data['page'],(int)$data['pagesize'])
            ->select();
        if($orderAll){
            return $this->ok('1','查询成功',$orderAll);
        }else{
            return $this->no('-1','查询失败');

        }


    }

    //待配送订单
    public  function  waitSend(){
        return $this->view->fetch();
    }

    public function waitSendShow(){
        $request= Request::instance();
        $data =$request->param();
        $orderAll=Db::name('pay_info')
            ->alias('a')
            ->field('a.*,b.frame_no')
            ->join('__CAR__ b','a.LicenseNo=b.car_no')
            ->where('openid',session('useropenid'))
            ->where('is_pay',2)
            ->order('id desc')
            ->limit((int)$data['page'],(int)$data['pagesize'])
            ->select();
        if($orderAll){
            return $this->ok('1','查询成功',$orderAll);
        }else{
            return $this->no('-1','查询失败');
        }
    }

    //已完成订单
    public  function  overPlay(){
        return $this->view->fetch();
    }
    public  function  overPlayShow(){
        $request= Request::instance();
        $data =$request->param();
        $orderAll=Db::name('pay_info')
            ->alias('a')
            ->field('a.*,b.frame_no')
            ->join('__CAR__ b','a.LicenseNo=b.car_no')
            ->where('openid',session('useropenid'))
            ->where('is_pay',2)
            ->order('id desc')
            ->limit((int)$data['page'],(int)$data['pagesize'])
            ->select();
        if($orderAll){
            return $this->ok('1','查询成功',$orderAll);
        }else{
            return $this->no('-1','查询失败');
        }

    }

        /**
         * 订单详情展示
         */
    public  function  orderDetail(){
        $request= Request::instance();
        $id =(int)$request->param('id');
        $detail=Db::name('pay_info')
            ->alias('a')
            ->field('a.*,b.*')
            ->join('__CAR__ b','a.LicenseNo=b.car_no')
            ->where('a.id',$id)
            ->find();
        $this->view->assign('detail',$detail);
        return $this->view->fetch();
    }
    /**
     * 上传两证
     */
    public function uploading(){
        $request= Request::instance();
        $order_id =(int)$request->param('order_id');
        $this->view->assign('order_id',$order_id);
        return $this->view->fetch();
    }

    /**
     * 上传证件信息
     */
    public function addOrderInfo()
    {
        $request = Request::instance();
        $order_id =(int) $request->param('id');
        $idcard_pro = trim($request->param('idcard_pro'));
        $idcard_con = trim($request->param('idcard_con'));
        $driving_pro = trim($request->param('driving_pro'));
        $driving_con = trim($request->param('driving_con'));

        $data = [
            'idcard_pro' => $idcard_pro,
            'idcard_con' => $idcard_con,
            'driving_pro' => $driving_pro,
            'driving_con'=> $driving_con
        ];
        try {
            $orderInfo = Db::name('pay_info')->where('id',$order_id)->update($data);
            if ($orderInfo) {
                return $this->ok('1', '信息已提交,等待审核', $orderInfo);
            } else {
                return $this->no('-1', '系统繁忙，请稍后再试');
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


}