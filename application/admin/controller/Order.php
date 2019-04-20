<?php
namespace app\admin\controller;


use app\admin\Controller;
use app\common\model\Order as OrderModel;
use think\Db;

class Order extends Controller
{

    /**
     * 列表
     */
    public function index(){
        //根据条件查询
        $map=[];
        if ($this->request->param("")) {
            $map['LicenseNo'] = ["like", "%" . $this->request->param("car_no") . "%"];
        }
        if ($this->request->param("order_no")) {
            $map['PayNum'] = ["like", "%" . $this->request->param("order_no") . "%"];
        }


        $listRows = 10;
        $list = Db::name('pay_info')
              ->alias("a")
              ->field('a.*,b.nickname as user_name')
              ->join('__USER__ b','a.openid=b.open_id')
              ->order('id desc')
              ->where($map)
              ->paginate($listRows, false, ['query' => $this->request->get()]);

        $count = count($list);
        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());

        $model=new OrderModel();
        $orderStatus=$model->getOrderStatus();
        $payMode=$model->getPayMode();
        $this->view->assign('orderStatus',$orderStatus);
        $this->view->assign('payMode',$payMode);
        return $this->view->fetch();
    }
    /**
     * 修改
     * @return mixed
     */
    public function edit()
    {
        $id=$this->request->param('id');
        if ($this->request->isAjax()) {
            $data = $this->request->post();
            $data['update_time']=time();
            $goods = Db::name('order')->where('id', $id)->update($data);
            if ($goods) {
                return ajax_return_adv('修改成功');
            } else {
                return ajax_return_adv('修改失败');
            }
        } else {
            //获取到该数据的值
            if (!$id) {
                throw new Exception("缺少参数ID");
            }
            $list =  Db::name('order')->where('id', $id)->find();
            if (!$list) {
                throw new HttpException(404, '该记录不存在');
            }
            $this->view->assign('list', $list);
            $model=new OrderModel();
            $orderStatus=$model->getOrderStatus();
            $payMode=$model->getPayMode();
            $this->view->assign('orderStatus',  $orderStatus);
            $this->view->assign('payMode',$payMode);
            return $this->view->fetch();

        }
    }
    public function detail(){
        $id=$this->request->param('id');

        $list=Db::name('pay_info')
            ->alias("a")
            ->field('a.*,b.nickname as user_name')
            ->join('__USER__ b','a.openid=b.open_id')
            ->where('a.id',$id)
            ->find();

        $this->view->assign('list', $list);
        return $this->view->fetch();
    }

}
