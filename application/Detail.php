<?php
namespace app\nvjian\controller;

use think\Db;
use think\Request;
use think\Response;
use think\Controller;

class Detail extends Controller
{

    //政工快讯
    public function zhengcekuaixun()
    {
        if ($this->request->isGet()) {
            $id = input('get.id');
            $data = Db::name('zhenggongkuaixun')->where('id', $id)->find();
            $this->view->assign('data', $data);
            return $this->view->fetch();
        }
    }

    //警务平台
    public function jingwupintai()
    {
        if ($this->request->isGet()) {
            $id = input('get.id');
            $data = Db::name('jingwupintai')->where('id', $id)->find();
        $this->view->assign('data', $data);
        return $this->view->fetch();
     }
    }
        //教育培训
    public function jiaoyupeixun(){
        if ($this->request->isGet()) {
            $id = input('get.id');
            $data=Db::name('jiaoyupeixun')->where('id', $id)->find();
        $this->view->assign('data',$data);
        return $this->view->fetch();
    }
    }

        //警苑清风
     public function jingyuanqingfeng(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('jingyuanqingfeng')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //思廉园地
     public function silianyuandi(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('silianyuandi')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
    }
    }
        //审计工作
     public function shenjigongongzuo(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('shenjigongzuo')->where('id', $id)->find();
        $this->view->assign('data',$data);
        return $this->view->fetch();
    }
    }

        //狱政管理
     public function yuzhengguanli(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('yuzhengguanli')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //刑罚执行
     public function xingfazhixing(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('xingfazhixing')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //指挥中心
     public function zhihuizhongxin(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('zhihuizhongxin')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //企业信息
     public function qiyexinxi(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('qiyexinxi')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //安全生产
     public function  anquanshengchang(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('anquanshengchang')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //生产报表
     public function shengchangbaobiao(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('shengchangbaobiao')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //教改工作
     public function jiaogaigongzuo(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('jiaogaigongzuo')->where('id', $id)->find();
          $this->view->assign('data',$data);
          return $this->view->fetch();
     }
     }
        //生卫管理
     public function shengweiguanli(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('shengweiguanli')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //娇治评估
     public function jiaozhipingu(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('jiaozhipingu')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //狱内科技
     public function yuneikeji(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('yuneikeji')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //维修保障
     public function weixiubaozhang(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('weixiubaozhang')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }
        //后勤服务
     public function houqingfuwu(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('houqingfuwu')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
     }

        //多彩女监
     public function duocainvjian(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('duocainvjian')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
    }

        //栏目
     public function lanmu(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('lanmu')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
    }
        //留言板
     public function liuyanban(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('liuyanban')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
    }
        //综合服务
     public function zonghefuwu(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('zonghefuwu')->where('id', $id)->find();
        $this->view->assign('data',$data);
         return $this->view->fetch();
     }
    }
        //专题活动
     public function zhuantihuodongfenlei(){
         if ($this->request->isGet()) {
             $id = input('get.id');
             $data=Db::name('zhuantihuodongwenzhang')->where('fenlei_id', $id)->find();
          $this->view->assign('data',$data);
         return $this->view->fetch();
     }
    }



}
