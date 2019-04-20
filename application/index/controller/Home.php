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
// 首页控制器
//-------------------------

namespace app\index\controller;


use think\Db;
use think\Config;


class Home extends Base
{

    /**
     * 首页
     */
    public function index(){
         //轮播图
         $banner = Db::name('banner')->order('sort asc')->select();
        //技术合作
         $pingpai = Db::name('partner')->where('class',0)->order('sort asc')->where('isdelete',0)->select();
        //品牌合作
         $jishu = Db::name('partner')->where('class',1)->order('sort asc')->where('isdelete',0)->select();
         //最新资讯
         $news = Db::name('industry_news')->order('id desc')->limit('5')->where('isdelete',0)->select();

        $userInfo=Db::name('user')->field('id')->where('open_id',session('useropenid'))->find();
        $this->view->assign('user_id',$userInfo['id']);

         $this->view->assign('pingpai',$pingpai);
         $this->view->assign('jishu',$jishu);
         $this->view->assign('banner',$banner);
         $this->view->assign('news',$news);
         return $this->view->fetch();

    }
    //增值服务
    public function server(){
        //用户的信息
        $userInfo=Db::name('user')->field('id,avatar,phone')->where('open_id',session('useropenid'))->find();
        $this->view->assign('userInfo',$userInfo);
        //电话号码
        $about_us= Db::name('about_us')->field('tel')->find();
        $this->view->assign('about_us',$about_us);
        return $this->view->fetch();
    }
    //分享
    public function share1(){
        $about_us= Db::name('about_us')->field('activity')->find();
        $this->view->assign('about_us',$about_us);

        $userInfo=Db::name('user')->where('open_id',session('useropenid'))->find();
        $this->view->assign('data',$userInfo);
        return $this->view->fetch();
    }
   //分享
    public function share(){
        if(!session('useropenid')){
            $this->redirect('index/login/index');
        }
        $userId=$this->request->param('user_id');
       if(!$userId){
           $this->redirect('index/login/index');
       }
        $userInfo=Db::name('user')->field('id,qrcode,qrcode_big')->where('id',$userId)->find();
        if($userInfo['qrcode_big']){
            $this->view->assign('data',$userInfo);
        }else{
            header('Content-Type: image/png');
            vendor("phpqrcode.phpqrcode");//引入工具包


            session('imgpath',$userInfo['qrcode']);



            $path_1 = "http://www.fengbaowang.net/public/static/index/images/share.png";
            $path_2 = "http://www.fengbaowang.net/".session('imgpath');

            $image_1 = imagecreatefrompng($path_1);
            $image_2 = imagecreatefrompng($path_2);
            $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
            $color = imagecolorallocate($image_3, 255, 255, 255);
            imagefill($image_3, 0, 0, $color);
            imageColorTransparent($image_3, $color);
            imagecopyresampled($image_3,$image_1,0,0,0,0,imagesx($image_1),imagesy($image_1),imagesx($image_1),imagesy($image_1));
            imagecopymerge($image_3,$image_2,275,277,0,0,imagesx($image_2),imagesy($image_2), 100);
            //将画布保存到指定的gif文件
            $fn='public/Uploads/QRcodeBig/'.time().'.jpg';

            // $im= '/Uploads/QRcode/'.$time;
            imagejpeg($image_3, $fn);
            //将数据进行销毁
            @unlink($path_2);

          //  $info['qrcode_big']=$fn;

            Db::name('user')->where('id',$userInfo['id'])->setField('qrcode_big',$fn);

            $this->redirect('index/home/share');
        //    $this->view->assign('data',$info);
        }
        return  $this->view->fetch();
    }








    //首页加载完执行代码
    public function jiazai(){
        //查询订单状态
        $orderData=Db::name('pay_info')
            ->alias('a')
            ->field('a.*,b.frame_no')
            ->join('__CAR__ b','a.LicenseNo=b.car_no')
            ->where('openid',session('useropenid'))
            ->select();

        foreach($orderData as $v){
            //未付款
           /* if($v['is_pay']==1){
                $stage_set = Db::name('stage_set')->find();
                //设置奖励金额
                $bonus=$v['Money']*$stage_set['one_stage']/100;
                Db::name('pay_info')->where('id',$v['id'])->setField('bonus',$bonus);
            }*/
            //已付款
            if($v['is_pay']!==1 &&(int)$v['is_bonus']!==1){
                $Enquiry= new Enquiry();
                $res=$Enquiry->pay_info($v['BiztNo'],$v['ForcetNo'],$v['frame_no'],$v['LicenseNo'],$v['ChannelId'],$v['Source']);
                $res=json_decode($res);

                if((int)$res->BusinessStatus==1){
                    // 启动事务
                    Db::startTrans();
                    try {
                            Db::name('pay_info')->where('id',$v['id'])->setField('is_pay',2);
                            //
                            $stage_set = Db::name('stage_set')->find();
                            //设置奖励金额
                          /*  $bonus=$v['Money']*$stage_set['one_stage']/100;
                            Db::name('pay_info')->where('id',$v['id'])->setField('bonus',$bonus);*/
                            Db::name('pay_info')->where('id',$v['id'])->setField('is_bonus',1);
                            $this->fenxiao(session('useropenid'));

                        // 提交事务
                        Db::commit();
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                        throw new Exception($e->getMessage());
                    }
                }else{
                    Db::name('pay_info')->where('id',$v['id'])->setField('is_pay',1);
                }
            }

        }
    }
}