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
// 帮助中心
//-------------------------

namespace app\index\controller;


use think\Db;
use think\Request;

class Htlp extends Base
{
    /**
     * 关于我们
     *
     */
   public  function  aboutUs(){
       $about= Db::name('about_us')->find();
       $this->view->assign('about',$about);
       return $this->view->fetch();
   }
    /**
     * 用户指南
     *
     */
    public  function  help(){
        $help= Db::name('help')->find();
        $this->view->assign('help',$help);
        return $this->view->fetch();
    }

    /**
     * 联系我们
     *
     */
    public  function  contact(){
        $contact= Db::name('about_us')->field('tel')->find();
        $this->view->assign('contact',$contact);
        return $this->view->fetch();
    }

    /**
     * 我要吐槽
     *
     */
    public  function  opinon(){
        $request= Request::instance();
        $user_id =(int)$request->param('user_id');

        $this->view->assign('user_id',$user_id);
        return $this->view->fetch();
    }

    /**
     * 意见提交
     */
    public  function  opinionAdd(){
        $request= Request::instance();
        //验证手机号码
        $content =trim($request->param('content'));
        if(!$content){
            return $this->no('-1','意见留言不能为空');
        }
        $user_id =(int)$request->param('user_id');
        $photos=$request->param('photos');

        $data=[
            'user_id'=>$user_id,
            'content'=>$content,
            'photos'=>$photos,
            'create_time'=>time()
        ];
        $res=Db::name('opinion')->insert($data);
        if(!$res){
            return  $this->no('-1','系统繁忙，请稍后再试');
        }else{
            return  $this->ok('1','留言成功');
        }
    }
}