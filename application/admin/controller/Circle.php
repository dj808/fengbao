<?php
namespace app\admin\controller;


use app\admin\Controller;
use think\Db;


/**
 * 圈子管理
 * Class Circle
 * @package app\admin\controller
 */
class Circle extends Controller
{

    /**
     * 列表
     */
    public function index(){

        $list = Db::name('user')
              ->field('id,nickname,avatar,note,invite_id')
              ->order('id desc')
              ->where('invite_id','neq','')
              ->select();
        foreach ($list as $k=>$v){
            $list[$k]['user_name']=Db::name('user')->field('id,nickname,avatar')->where('id',$v['invite_id'])->find();
        }
        $count = count($list);
        $this->view->assign('list',$list);
        $this->view->assign("count", $count);
       // $this->view->assign("page", $list->render());

        return $this->view->fetch();
    }


}
