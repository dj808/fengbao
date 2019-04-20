<?php
namespace app\admin\controller;

use app\admin\Controller;
use think\Db;
use app\common\model\Agent as AgentModel;

class Agent extends Controller
{


    /**
     * 代理人家园首页
     */
    public function index(){
        //根据条件查询
        $map=[];
        $map['isdelete']=0;
        if ($this->request->param("agent_name")) {
            $map['agent_name'] = ["like", "%" . $this->request->param("agent_name") . "%"];
        }
        if ($this->request->param("phone")) {
            $map['phone'] = ["like", "%" . $this->request->param("phone") . "%"];
        }
        if ($this->request->param("company_name")) {
            $map['company_name'] = ["like", "%" . $this->request->param("company_name") . "%"];
        }
        if (Null !=$this->request->param("check_status")) {
            $map['check_status'] = ["eq",  $this->request->param("check_status")];
        }

        $listRows = 10;
        $list = Db::name('agent')->order('id desc')->where($map)->paginate($listRows, false, ['query' => $this->request->get()]);
        $listnum = Db::name('agent')->field('id')->where($map)->select();
        $count = count($listnum);
        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());

        $model=new AgentModel();
        $agentType=$model->getAgentType();
        $statusType=$model->getStatusType();
        $this->view->assign("agentType", $agentType);
        $this->view->assign("statusType", $statusType);
        return $this->view->fetch();
    }
    /**
     * 添加
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            // 写入数据
            try {
                $new = $this->request->post();
                $new['create_time']=time();
                $info = Db::name('agent')->insert($new);
                // 提交事务
                  if($info){
                      return ajax_return_adv('添加成功');
                  }else{
                      return ajax_return_adv('添加失败');
                  }
            } catch (\Exception $e) {
                return ajax_return_adv_error($e->getMessage());
            }

        } else {
            $model=new AgentModel();
            $agentType=$model->getAgentType();
            $statusType=$model->getStatusType();
            $this->view->assign('agentType',$agentType);
            $this->view->assign('statusType',$statusType);
            return $this->view->fetch();


        }

    }
    /**
     * 修改
     * @return mixed
     */
    public function edit()
    {

        $controller = $this->request->controller();
        if ($this->request->isAjax()) {
            $id=$this->request->param('id');
            $data = $this->request->post();
            $data['check_time']=time();
            $goods = Db::name('agent')->where('id', $id)->update($data);

            if ($goods) {
                return ajax_return_adv('修改成功');
            } else {
                return ajax_return_adv('修改失败');
            }
        } else {
            //获取到该数据的值
            $id = $this->request->param('id');
            if (!$id) {
                throw new Exception("缺少参数ID");
            }
            $vo =  Db::name('agent')->where('id', $id)->find();
            if (!$vo) {
                throw new HttpException(404, '该记录不存在');
            }
            $this->view->assign('list', $vo);
            $model=new AgentModel();
            $agentType=$model->getAgentType();
            $statusType=$model->getStatusType();
            $this->view->assign('agentType',$agentType);
            $this->view->assign('statusType',$statusType);

            return $this->view->fetch();
        }
    }

    public function delete()
    {
        return $this->updateField($this->fieldIsDelete, 1, "移动到回收站成功");
    }
}
