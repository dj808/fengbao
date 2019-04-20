<?php
namespace app\admin\controller;


use app\admin\Controller;
use think\Db;
use think\Exception;
use app\admin\controller\Demo;

class Car extends Controller
{


    //行业资讯首页
    public function index(){
        //根据条件查询
        $map=[];
        if ($this->request->param("car_no")) {
            $map['car_no'] = ["like", "%" . $this->request->param("car_no") . "%"];
        }

        $listRows = 10;
        $list = Db::name('car')->order('id desc')->where($map)->paginate($listRows, false, ['query' => $this->request->get()]);
        $listnum = Db::name('car')->field('id')->where($map)->select();
        $count = count($listnum);
        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());

        return $this->view->fetch();
    }




    public function edit(){
        $controller = $this->request->controller();
        if ($this->request->isPost()) {
            $id=$this->request->param('id');
            $new = $this->request->post();
            $new['update_time']=time();
            $goods = Db::name('car')->where('id', $id)->update($new);
            if ($goods) {
                return ajax_return_adv('修改成功');
            } else {
                return ajax_return_adv_error('修改失败');
            }
        } else {
            //获取到该数据的值
            $id = $this->request->param('id');
            if (!$id) {
                throw new Exception("缺少参数ID");
            }
            $info = $this->getModel($controller)->find($id);
            if (!$info) {
                throw new HttpException(404, '该记录不存在');
            }
            $this->view->assign("list", $info);
            return $this->view->fetch();
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        return $this->updateField($this->fieldIsDelete, 1, "移动到回收站成功");
    }


}
