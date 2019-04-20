<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;
use app\common\model\User;

class Opinion extends Controller
{
    use \app\admin\traits\controller\Controller;
    // 方法黑名单
    protected static $blacklist = [];
    /**
     * 显示页
     * @return mixed
     */
    public  function index(){
        $listRows = 10;
        $list = Db::name('opinion')
            ->alias('a')
            ->field('a.*,b.nickname')
            ->join('__USER__ b ','a.user_id=b.id')
            ->order('a.id desc')
            ->where('a.isdelete','0')
            ->paginate($listRows, false, ['query' => $this->request->get()]);

        $listnum =$list->toArray();
        $count = intval($listnum['total']);
        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());

        return $this->view->fetch();
    }
    public  function detail(){
        $id=$this->request->param('id');
        $list=Db::name('opinion')->where('id',$id)->find();

        $photos=explode(',',$list['photos']);
        unset($photos[0]);
        $this->view->assign('list', $list);
        $this->view->assign('photos', $photos);
        return $this->view->fetch();

    }
    
}
