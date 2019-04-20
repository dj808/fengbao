<?php
namespace app\nvjian\controller;

use app\nvjian\Controller;
use think\Loader;
use think\Session;
use think\Db;
use think\Config;
use think\Exception;
use think\View;
use think\Request;
use think\exception\HttpResponseException;
use think\Response;
use think\Validate;

class Index extends Controller
{
    public function index()
    {
    	$Faisheigirlinfos1 = Db::name('Faisheigirladd')->where('types',1)->order('block desc')->select();
    	$Faisheigirlinfos2 = Db::name('Faisheigirladd')->where('types',2)->order('block desc')->select();
    	$Faisheigirlinfos3 = Db::name('Faisheigirladd')->where('types',3)->order('block desc')->select();
    	$wuzhou = Db::name('wuzhou')->order('block desc')->select();//五洲来风
    	$kqgcinfo = Db::name('kqgc')->order('block desc')->select();//铿锵广场分类
    	$kqgcinfos1 = Db::name('kqgcadd')->where('types',1)->order('block desc')->select();//铿锵广场
    	$kqgcinfos2 = Db::name('kqgcadd')->where('types',2)->order('block desc')->select();
    	$kqgcinfos3 = Db::name('kqgcadd')->where('types',3)->order('block desc')->select();
    	$tp_bamlftype = Db::name('bamlftype')->order('block desc')->select();//八面来风分类
    	$tp_bamlfadd1 = Db::name('bamlfadd')->where('types',1)->order('block desc')->select();//八面来风
    	$tp_bamlfadd2 = Db::name('bamlfadd')->where('types',2)->order('block desc')->select();
    	$tp_bamlfadd3 = Db::name('bamlfadd')->where('types',3)->order('block desc')->select();
    	$bamlftypeinfo = Db::name('bamlftype')->order('block desc')->select();

    	$this->view->assign('tp_bamlftype', $tp_bamlftype);
    	$this->view->assign('tp_bamlfadd1', $tp_bamlfadd1);
    	$this->view->assign('tp_bamlfadd2', $tp_bamlfadd2);
    	$this->view->assign('tp_bamlfadd3', $tp_bamlfadd3);
    	$this->view->assign('kqgcinfos1', $kqgcinfos1);
    	$this->view->assign('kqgcinfos2', $kqgcinfos2);
    	$this->view->assign('kqgcinfos3', $kqgcinfos3);
    	$this->view->assign('kqgcinfo', $kqgcinfo);
    	$this->view->assign('bamlftypeinfo', $bamlftypeinfo);
    	$this->view->assign('wuzhou', $wuzhou);
    	$this->view->assign('Faisheigirlinfos1', $Faisheigirlinfos1);
    	$this->view->assign('Faisheigirlinfos2', $Faisheigirlinfos2);
    	$this->view->assign('Faisheigirlinfos3', $Faisheigirlinfos3);

//zq  start
        // author zq 03月05  头部轮播
        $headlb = Db::name('headlb')->order('id desc')->limit('0,5')->select();
        $this->view->assign('headlb',$headlb);

// author zq 03月05  星光灿烂
        $xgcl = Db::name('xgcl')->order('id desc')->limit('0,5')->select();
        $this->view->assign('xgcl',$xgcl);



        // author zq 03月05  通知公告

        $tongzhigonggao = Db::name('tongzhigonggao')->order('id desc')->limit('0,5')->select();
        $this->view->assign('tongzhigonggao',$tongzhigonggao);



        // author zq 03月05  日督查通报

        $riduchatongbao = Db::name('riduchatongbao')->order('id desc')->limit('0,5')->select();
        $this->view->assign('riduchatongbao',$riduchatongbao);



        // author zq 03月05  生产日报

        $shengchanribao = Db::name('shengchanribao')->order('id desc')->limit('0,5')->select();
        $this->view->assign('shengchanribao',$shengchanribao);


        // author zq 03月05  狱情周报

        $yuqingzhoubao = Db::name('yuqingzhoubao')->order('id desc')->limit('0,5')->select();
        $this->view->assign('yuqingzhoubao',$yuqingzhoubao);


        // author zq 03月05  上级来文

        $shangjilaiwen = Db::name('shangjilaiwen')->order('id desc')->limit('0,5')->select();
        $this->view->assign('shangjilaiwen',$shangjilaiwen);


        // author zq 03月05  公共信息

        $gonggongxinxi = Db::name('gonggongxinxi')->order('id desc')->limit('0,5')->select();
        $this->view->assign('gonggongxinxi',$gonggongxinxi);

        // author f 03月05  监狱快讯

        $jianyukuaixun = Db::name('jianyukuaixun')->order('id desc')->limit('0,5')->select();
        foreach ($jianyukuaixun as $k=>$v){
            $jianyukuaixun[$k]['create_time']=date("Y-m-d", $v['create_time']);
        }
        $this->view->assign('jianyukuaixun',$jianyukuaixun);

        // author f 03月05  监狱公文

        $jianyugongwen = Db::name('jianyugongwen')->order('id desc')->limit('0,5')->select();
        foreach ($jianyugongwen as $k=>$v){
            $jianyugongwen[$k]['create_time']=date("Y-m-d", $v['create_time']);
        }
        $this->view->assign('jianyugongwen',$jianyugongwen);


//zq  end


        //   dj  start
		//政工快讯
        $zhengcekuaixun=Db::name('zhenggongkuaixun')->select();
        $this->view->assign('zhengcekuaixun',$zhengcekuaixun);
        //警务平台
        $jingwupintai=Db::name('jingwupintai')->select();
        $this->view->assign('jingwupintai',$jingwupintai);
        //教育培训
        $jiaoyupeixun=Db::name('jiaoyupeixun')->select();
        $this->view->assign('jiaoyupeixun',$jiaoyupeixun);
        //警苑清风
        $jingyuanqingfeng=Db::name('jingyuanqingfeng')->select();
        $this->view->assign('jingyuanqingfeng',$jingyuanqingfeng);
        //思廉园地
        $silianyuandi=Db::name('silianyuandi')->select();
        $this->view->assign('silianyuandi',$silianyuandi);
        //审计工作
        $shenjigongongzuo=Db::name('shenjigongzuo')->select();
        $this->view->assign('shenjigongongzuo',$shenjigongongzuo);

        //狱政管理
        $yuzhengguanli=Db::name('yuzhengguanli')->select();
        $this->view->assign('yuzhengguanli',$yuzhengguanli);
        //刑罚执行
        $xingfazhixing=Db::name('xingfazhixing')->select();
        $this->view->assign('xingfazhixing',$xingfazhixing);
        //指挥中心
        $zhihuizhongxin=Db::name('zhihuizhongxin')->select();
        $this->view->assign('zhihuizhongxin',$zhihuizhongxin);
        //企业信息
        $qiyexinxi=Db::name('qiyexinxi')->select();
        $this->view->assign('qiyexinxi',$qiyexinxi);
        //安全生产
        $anquanshengchang=Db::name('anquanshengchang')->select();
        $this->view->assign('anquanshengchang',$anquanshengchang);
        //生产报表
        $shengchangbaobiao=Db::name('shengchangbaobiao')->select();
        $this->view->assign('shengchangbaobiao',$shengchangbaobiao);
        //教改工作
        $jiaogaigongzuo=Db::name('jiaogaigongzuo')->select();
        $this->view->assign('jiaogaigongzuo',$jiaogaigongzuo);
        //生卫管理
        $shengweiguanli=Db::name('shengweiguanli')->select();
        $this->view->assign('shengweiguanli',$shengweiguanli);
        //娇治评估
        $jiaozhipingu=Db::name('jiaozhipingu')->select();
        $this->view->assign('jiaozhipingu',$jiaozhipingu);
        //狱内科技
        $yuneikeji=Db::name('yuneikeji')->select();
        $this->view->assign('yuneikeji',$yuneikeji);
        //维修保障
        $weixiubaozhang=Db::name('weixiubaozhang')->select();
        $this->view->assign('weixiubaozhang',$weixiubaozhang);
        //后勤服务
        $houqingfuwu=Db::name('houqingfuwu')->select();
        $this->view->assign('houqingfuwu',$houqingfuwu);

        //多彩女监
        $duocainvjian=Db::name('duocainvjian')->select();
        $this->view->assign('duocainvjian',$duocainvjian);

        //栏目
        $lanmu=Db::name('lanmu')->select();
        $this->view->assign('lanmu',$lanmu);
        //留言板
        $liuyanban=Db::name('liuyanban')->select();
        $this->view->assign('liuyanban',$liuyanban);
        //综合服务
        $zonghefuwu=Db::name('zonghefuwu')->select();
        $this->view->assign('zonghefuwu',$zonghefuwu);
        //专题活动
        $zhuantihuodongfenlei=Db::name('zhuantihuodongfenlei')->select();
        $this->view->assign('zhuantihuodongfenlei',$zhuantihuodongfenlei);
		
	
		
     //   dj  end






        return $this->view->fetch();
    }
    // 时尚女性详情
    public function faisheigirladdinfo()
    {
    	$id = $this->request->param('id');
    	$faisheigirladd = Db::name('Faisheigirladd')->where('id',$id)->find();
    	$this->view->assign('faisheigirladd', $faisheigirladd);
    	return $this->view->fetch();
    }
    // 五洲来风详情
    public function wuzhouinfo()
    {
    	$id = $this->request->param('id');
    	$wuzhou = Db::name('wuzhou')->where('id',$id)->find();
    	$this->view->assign('wuzhou', $wuzhou);
    	return $this->view->fetch();
    }
    // 八面来风详情
    public function bamianinfo()
    {
    	$id = $this->request->param('id');
    	$bamlfadds = Db::name('bamlfadd')->where('id',$id)->find();
    	$this->view->assign('bamlfadds', $bamlfadds);
    	return $this->view->fetch();
    }
    // 铿锵广场详情
    public function kqgcinfo()
    {
    	$id = $this->request->param('id');
    	$kqgcadd = Db::name('kqgcadd')->where('id',$id)->find();
    	$this->view->assign('kqgcadd', $kqgcadd);
    	return $this->view->fetch();
    }

    // author zq 03月05  通知公告详情页
    public function tzjg_detail(){
        $id=$this->request->param('id');
        $qiqi=Db::name('tongzhigonggao')->where('id',$id)->find();
        $this->view->assign('qiqi',$qiqi);
        return $this->view->fetch();
    }

    // author zq 03月05  日督查通报详情页
    public function rdctb_detail(){
        $id=$this->request->param('id');
        $qiqi=Db::name('riduchatongbao')->where('id',$id)->find();
        $this->view->assign('qiqi',$qiqi);
        return $this->view->fetch();
    }

    // author zq 03月05  生产日报详情页
    public function scrb_detail(){
        $id=$this->request->param('id');
        $qiqi=Db::name('shengchanribao')->where('id',$id)->find();
        $this->view->assign('qiqi',$qiqi);
        return $this->view->fetch();
    }

    // author zq 03月05  狱情周报详情页
    public function yqzb_detail(){
        $id=$this->request->param('id');
        $qiqi=Db::name('tongzhigonggao')->where('id',$id)->find();
        $this->view->assign('qiqi',$qiqi);
        return $this->view->fetch();
    }

    // author zq 03月05  上级来文
    public function sjlw_detail(){
        $id=$this->request->param('id');
        $qiqi=Db::name('shangjilaiwen')->where('id',$id)->find();
        $this->view->assign('qiqi',$qiqi);
        return $this->view->fetch();
    }

// author zq 03月05  公共信息
    public function ggxx_detail(){
        $id=$this->request->param('id');
        $qiqi=Db::name('gonggongxinxi')->where('id',$id)->find();
        $this->view->assign('qiqi',$qiqi);
        return $this->view->fetch();
    }

    //监狱简介
    public function jianyujianjie()
    {
        $listRows = 15;
        $jianyujianjie = Db::name('jianyujianjie')->where('status',1)->paginate($listRows, false, ['query' => $this->request->get()]);
        //print_r($list);die;
        $this->view->assign("page", $jianyujianjie->render());
        $jianyujianjie = $jianyujianjie->all();

        foreach ($jianyujianjie as $k=>$v){
            $jianyujianjie[$k]['create_time']=date("Y-m-d h:i:s", $v['create_time']);
        }

        $this->view->assign('jianyujianjie',$jianyujianjie);
        //print_r($brand);die;
        return $this->view->fetch();
    }
    //监狱简介详情
    public function jianyujianjieshow()
    {
        $id=$this->request->param('id');
        $jianyujianjieshow = Db::name('jianyujianjie')->where('id',$id)->find();
        $jianyujianjieshow['create_time']=date("Y-m-d", $jianyujianjieshow['create_time']);
        $this->view->assign('jianyujianjieshow',$jianyujianjieshow);
        //print_r($newsDetail);die;
        return $this->view->fetch();
    }
    //监狱简介-领导信息
    public function lingdaoxinxi()
    {
        $listRows = 15;
        $jianyujianjie = Db::name('jianyujianjie')->where('status',2)->paginate($listRows, false, ['query' => $this->request->get()]);
        //print_r($list);die;
        $this->view->assign("page", $jianyujianjie->render());
        $jianyujianjie = $jianyujianjie->all();

        foreach ($jianyujianjie as $k=>$v){
            $jianyujianjie[$k]['create_time']=date("Y-m-d h:i:s", $v['create_time']);
        }

        $this->view->assign('jianyujianjie',$jianyujianjie);
        //print_r($brand);die;
        return $this->view->fetch();
    }
    //监狱简介-组织机构
    public function zuzhijigou()
    {
        $listRows = 15;
        $jianyujianjie = Db::name('jianyujianjie')->where('status',3)->paginate($listRows, false, ['query' => $this->request->get()]);
        //print_r($list);die;
        $this->view->assign("page", $jianyujianjie->render());
        $jianyujianjie = $jianyujianjie->all();

        foreach ($jianyujianjie as $k=>$v){
            $jianyujianjie[$k]['create_time']=date("Y-m-d h:i:s", $v['create_time']);
        }

        $this->view->assign('jianyujianjie',$jianyujianjie);
        //print_r($brand);die;
        return $this->view->fetch();
    }
    //部室监区
    public function bushijianqu()
    {
        $mid = $this->request->param('mid');
        $name['name'] = $this->request->param('name');
        if(empty($mid)){
            $listRows = 15;
            $bushijianqu = Db::name('bushijianqu')->where('status',1)->paginate($listRows, false, ['query' => $this->request->get()]);
            //print_r($list);die;
            $this->view->assign("page", $bushijianqu->render());
            $bushijianqu = $bushijianqu->all();
            foreach ($bushijianqu as $k=>$v){
                $bushijianqu[$k]['create_time']=date("Y-m-d h:i:s", $v['create_time']);
            }

            $name['name'] = '一监区';
            $this->view->assign('name',$name);
            $this->view->assign('bushijianqu',$bushijianqu);
            //print_r($brand);die;
            return $this->view->fetch();
        }else{
            $listRows = 15;
            $bushijianqu = Db::name('bushijianqu')->where('status',$mid)->paginate($listRows, false, ['query' => $this->request->get()]);
            //print_r($list);die;
            $this->view->assign("page", $bushijianqu->render());
            $bushijianqu = $bushijianqu->all();

            foreach ($bushijianqu as $k=>$v){
                $bushijianqu[$k]['create_time']=date("Y-m-d h:i:s", $v['create_time']);
            }
            $this->view->assign('name',$name);
            $this->view->assign('bushijianqu',$bushijianqu);
            //print_r($brand);die;
            return $this->view->fetch();
        }

    }
    //部室监区详情
    public function bushijianqushow()
    {
        $id=$this->request->param('id');
        $bushijianqushow = Db::name('bushijianqu')->where('id',$id)->find();
        $bushijianqushow['create_time']=date("Y-m-d", $bushijianqushow['create_time']);
        $this->view->assign('bushijianqushow',$bushijianqushow);
        //print_r($newsDetail);die;
        return $this->view->fetch();
    }
    //监狱快讯
    public function jianyukuaixun()
    {
        $listRows = 15;
        $jianyukuaixun = Db::name('jianyukuaixun')->paginate($listRows, false, ['query' => $this->request->get()]);
        //print_r($list);die;
        $this->view->assign("page", $jianyukuaixun->render());
        $jianyukuaixun = $jianyukuaixun->all();

        foreach ($jianyukuaixun as $k=>$v){
            $jianyukuaixun[$k]['create_time']=date("Y-m-d h:i:s", $v['create_time']);
        }

        $this->view->assign('jianyukuaixun',$jianyukuaixun);
        //print_r($brand);die;
        return $this->view->fetch();
    }
    ///监狱快讯详情
    public function jianyukuaixunshow()
    {
        $id=$this->request->param('id');
        $jianyukuaixunshow = Db::name('jianyukuaixun')->where('id',$id)->find();
        $jianyukuaixunshow['create_time']=date("Y-m-d", $jianyukuaixunshow['create_time']);
        $this->view->assign('jianyukuaixunshow',$jianyukuaixunshow);
        //print_r($newsDetail);die;
        return $this->view->fetch();
    }
    //政策法规
    public function zhengcefagui()
    {
        $listRows = 15;
        $zhengcefagui = Db::name('zhengcefagui')->paginate($listRows, false, ['query' => $this->request->get()]);
        //print_r($list);die;
        $this->view->assign("page", $zhengcefagui->render());
        $zhengcefagui = $zhengcefagui->all();

        foreach ($zhengcefagui as $k=>$v){
            $zhengcefagui[$k]['create_time']=date("Y-m-d h:i:s", $v['create_time']);
        }
        $this->view->assign('zhengcefagui',$zhengcefagui);
        //print_r($brand);die;
        return $this->view->fetch();
    }
    //政策法规详情
    public function zhengcefaguishow()
    {
        $id=$this->request->param('id');
        $zhengcefaguishow = Db::name('zhengcefagui')->where('id',$id)->find();
        $zhengcefaguishow['create_time']=date("Y-m-d", $zhengcefaguishow['create_time']);
        $this->view->assign('zhengcefaguishow',$zhengcefaguishow);
        //print_r($newsDetail);die;
        return $this->view->fetch();
    }

    //监狱公文
    public function jianyugongwen()
    {
        $mid = $this->request->param('mid');
        $name['name'] = $this->request->param('name');
        if(empty($mid)){
            $listRows = 15;
            $jianyugongwen = Db::name('jianyugongwen')->where('status',1)->paginate($listRows, false, ['query' => $this->request->get()]);
            //print_r($list);die;
            $this->view->assign("page", $jianyugongwen->render());
            $jianyugongwen = $jianyugongwen->all();
            foreach ($jianyugongwen as $k=>$v){
                $jianyugongwen[$k]['create_time']=date("Y-m-d", $v['create_time']);
            }
            $name['name'] = '女监综合管理类';
            $this->view->assign('name',$name);
            $this->view->assign('jianyugongwen',$jianyugongwen);
            //print_r($brand);die;
            return $this->view->fetch();
        }else{
            $listRows = 15;
            $jianyugongwen = Db::name('jianyugongwen')->where('status',$mid)->paginate($listRows, false, ['query' => $this->request->get()]);
            //print_r($list);die;
            $this->view->assign("page", $jianyugongwen->render());
            $jianyugongwen = $jianyugongwen->all();

            foreach ($jianyugongwen as $k=>$v){
                $jianyugongwen[$k]['create_time']=date("Y-m-d", $v['create_time']);
            }
            $this->view->assign('name',$name);
            $this->view->assign('jianyugongwen',$jianyugongwen);
            //print_r($brand);die;
            return $this->view->fetch();
        }

    }

    //监狱公文详情
    public function jianyugongwenshow()
    {
        $id=$this->request->param('id');
        $jianyugongwenshow = Db::name('jianyugongwen')->where('id',$id)->find();
        $jianyugongwenshow['create_time']=date("Y-m-d", $jianyugongwenshow['create_time']);
        $this->view->assign('jianyugongwenshow',$jianyugongwenshow);
        //print_r($newsDetail);die;
        return $this->view->fetch();
    }

    //学习交流
    public function xuexijiaoliu()
    {
        $listRows = 15;
        $xuexijiaoliu = Db::name('xuexijiaoliu')->paginate($listRows, false, ['query' => $this->request->get()]);
        //print_r($list);die;
        $this->view->assign("page", $xuexijiaoliu->render());
        $xuexijiaoliu = $xuexijiaoliu->all();

        foreach ($xuexijiaoliu as $k=>$v){
            $xuexijiaoliu[$k]['create_time']=date("Y-m-d", $v['create_time']);
        }
        $this->view->assign('xuexijiaoliu',$xuexijiaoliu);
        //print_r($brand);die;
        return $this->view->fetch();
    }

    //学习交流详情
    public function xuexijiaoliushow()
    {
        $id=$this->request->param('id');
        $xuexijiaoliushow = Db::name('xuexijiaoliu')->where('id',$id)->find();
        $xuexijiaoliushow['create_time']=date("Y-m-d", $xuexijiaoliushow['create_time']);
        $this->view->assign('xuexijiaoliushow',$xuexijiaoliushow);
        //print_r($newsDetail);die;
        return $this->view->fetch();
    }

    //警苑文化
    public function jingyuanwenhua()
    {
        $listRows = 15;
        $jingyuanwenhua = Db::name('jingyuanwenhua')->paginate($listRows, false, ['query' => $this->request->get()]);
        //print_r($list);die;
        $this->view->assign("page", $jingyuanwenhua->render());
        $jingyuanwenhua = $jingyuanwenhua->all();

        foreach ($jingyuanwenhua as $k=>$v){
            $jingyuanwenhua[$k]['create_time']=date("Y-m-d", $v['create_time']);
        }
        $this->view->assign('jingyuanwenhua',$jingyuanwenhua);
        //print_r($brand);die;
        return $this->view->fetch();
    }

    //学习交流详情
    public function jingyuanwenhuashow()
    {
        $id=$this->request->param('id');
        $jingyuanwenhuashow = Db::name('jingyuanwenhua')->where('id',$id)->find();
        $jingyuanwenhuashow['create_time']=date("Y-m-d", $jingyuanwenhuashow['create_time']);
        $this->view->assign('jingyuanwenhuashow',$jingyuanwenhuashow);
        //print_r($newsDetail);die;
        return $this->view->fetch();
    }




}
