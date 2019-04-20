<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;
use app\common\model\LogMoney  as LogMoneyModel;

class LogMoney extends Controller
{
    use \app\admin\traits\controller\Controller;

    /**
     * 列表
     */
    public function index(){
        //根据条件查询

        $listRows = 10;
        $list = Db::name('log_money')
            ->alias('a')
            ->field('a.*,b.nickname as user_name')
            ->join('__USER__ b','a.user_id=b.id')
            ->order('id desc')

            ->paginate($listRows, false, ['query' => $this->request->get()]);
        $listnum = Db::name('log_money')->field('id')->select();
        $count = count($listnum);
        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());
        $moneyType=LogMoneyModel::getMoneyType();
        $this->view->assign('moneyType', $moneyType);
        return $this->view->fetch();
    }

    
}
