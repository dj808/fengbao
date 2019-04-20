<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;
use app\common\model\LogEnquiry as LogEnquiryModel;

class LogEnquiry extends Controller
{

    /**
     * 列表
     */
    public function index(){
        //根据条件查询
       /* $map=[];*/
       /* if ($this->request->param("user_id")) {
            $map['user_id'] = ["like", "%" . $this->request->param("user_id") . "%"];
        }*/
        $listRows = 10;
        $list = Db::name('log_enquiry')
            ->alias('a')
            ->field('a.*,b.nickname as user_name')
            ->join('__USER__ b','a.user_id=b.id')
            ->order('a.id desc')
            ->paginate($listRows, false, ['query' => $this->request->get()]);
      //  $listnum = count($list);
        $count = count($list);
        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());

        $model=new LogEnquiryModel();
        $carType= $model->getCarType();
        $insureType= $model->getInsureType();
        $this->view->assign('carType', $carType);
        $this->view->assign('insureType', $insureType);
        return $this->view->fetch();
    }

    
}
