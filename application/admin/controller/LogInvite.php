<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;
use app\common\model\LogInvite as LogInviteModel;

class LogInvite extends Controller
{
    use \app\admin\traits\controller\Controller;


    //邀请日志列表
    public function index()
    {
        //根据条件查询
        $map = [];
        if ($this->request->param("user_id")) {
            $map['user_id'] = ["like", "%" . $this->request->param("user_id") . "%"];
        }
        $inviteStatus=$this->request->param("invite_status");
        if (isset($inviteStatus)) {
                $map['invite_status'] = ["eq", $this->request->param("invite_status")];
            }
            $listRows = 10;
            $list = Db::name('log_invite')
                ->alias('a')
                ->field('a.*,b.nickname as user_name')
                ->join('__USER__ b','a.user_id=b.id')
                ->order('id desc')
                ->where($map)
                ->paginate($listRows, false, ['query' => $this->request->get()]);
            $listnum = Db::name('log_invite')->field('id')->where($map)->select();
            $count = count($listnum);
            $this->view->assign('list', $list);
            $this->view->assign("count", $count);
            $this->view->assign("page", $list->render());

            $statusType = LogInviteModel::getStatusType();
            $this->view->assign('statusType', $statusType);

            return $this->view->fetch();
        }

}
