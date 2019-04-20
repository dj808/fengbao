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
// 圈子控制器
//-------------------------

namespace app\index\controller;


use think\Db;
use think\Request;
use think\Image;
use think\Config;


class Circle extends Base
{
    /**
     *
     * 显示我的圈子并分享生成二维码的功能
     */
    public  function index(){
        if(!session('useropenid')){
            $this->redirect('index/login/index');
        }

        //设定邀请的人数
        $info=Db::name('about_us')->field('people_num')->find();
        //查看邀请的人数

        $userId=Db::name('user')->field('id')->where('open_id',session('useropenid'))->find();
        $inviteInfo= Db::name('user')
            ->field('id')
            ->where('invite_id',$userId['id'])
            ->where('phone','NEQ','')
            ->select();
        $num=count($inviteInfo);
        //还需邀请人数
        $numInvite=(int)$info['people_num']-$num;
        if($numInvite<0){
            $numInvite=0;
        }
        //判断用户是否已经出单
        $payInfo=Db::name('pay_info')->field('is_pay')->where('openid',session('useropenid'))->find();
        if($payInfo['is_pay']==2){
            $isPlay='已完成';
        }else{
            $isPlay='立即出单';
        }

        $this->view->assign('numInvite',$numInvite);
        $this->view->assign('info',$info);
        $this->view->assign('isPlay',$isPlay);
        $this->view->assign('user_id',$userId['id']);

        $wxConfig = Config::get('wechat');
        $WebUrl = $wxConfig['WebUrl'];
        $userInfo=Db::name('user')->where('open_id',session('useropenid'))->find();
        $webData=$WebUrl.'?invite_id='.$userInfo['id'];
        if($userInfo['qrcode']){
            $this->view->assign('data',$userInfo);
            return $this->view->fetch();
        }else{

            header('Content-Type: image/png');

            vendor("phpqrcode.phpqrcode");//引入工具包

            $qRcode = new \QRcode();

            $path = "public/Uploads/QRcode/";//生成的二维码所在目录

            $time=time().'.png';

            $fileName=$path.$time;

            $data=$webData;

            $level='L';

            $size=5;

            ob_end_clean();//清空缓冲区

            $qRcode::png($data,$fileName,$level, $size);
            session('imgpath',$fileName);
            $file_name = iconv("utf-8","gb2312",$time);

            $file_path = $_SERVER['DOCUMENT_ROOT'].'/'.$fileName; //获取下载文件的大小

         //   $file_size = filesize($file_path); //

            $file_temp = fopen ($file_path, "r" ); //返回的文件

            header("Content-type:application/octet-stream"); //按照字节大小返回

            header("Accept-Ranges:bytes"); //返回文件大小

         //   header("Accept-Length:".$file_size); //这里客户端的弹出对话框

            header("Content-Disposition:attachment;filename=".$time);

            Db::name('user')->where('open_id',session('useropenid'))->setField('qrcode', $fileName);

            //显示
           $info=Db::name('user')->where('open_id',session('useropenid'))->find();
            $this->redirect('index/circle/index');
          //  $this->view->assign('data',$info);
            exit;
        //    return $this->view->fetch();

        }


    }


    /*
     * 我的邀请记录
     */

     public  function inviteList(){
         $request= Request::instance();
         $user_id =(int)$request->param('user_id');
         $inviteInfo= Db::name('user')
           ->field('id,phone,nickname,avatar,note')
           ->where('invite_id',$user_id)
           ->where('phone','NEQ','')
           ->select();
         foreach($inviteInfo as $k=> $val ){
             $inviteInfo[$k]['phone']=substr($val['phone'], 0, 4).'****'.substr($val['phone'], 8);
         }
         $num=count($inviteInfo);
         $this->view->assign('inviteInfo',$inviteInfo);
         $this->view->assign('num',$num);
         return $this->view->fetch();
     }


     //修改邀请备注
     public function editNote(){
         $request= Request::instance();
         $note =htmlspecialchars($request->param('note'));
         $user_id =intval($request->param('user_id'));

         if(!$note){
             return $this->no('-1','备注不能为空');
         }
        $res=Db::name('user')->where('id',$user_id)->setField('note', $note);
         if($res){
             return $this->ok('1','备注成功');
         }else{
             return $this->no('-1','系统繁忙，请稍后再试');
         }

     }

    /**
     * 查看规则
     */
    public function getRule(){
        $rule = Db::name('rule')->find();
        $this->view->assign('rule',$rule);
        return  $this->view->fetch('rule');
    }


}