<?php
namespace app\admin\controller;


use app\admin\Controller;
use think\Db;
use think\Exception;
use app\admin\controller\Demo;

class IndustryNews extends Controller
{


    //行业资讯首页
    public function index(){
        //根据条件查询
        $map=[];
        $map['isdelete']=0;
        if ($this->request->param("title")) {
            $map['title'] = ["like", "%" . $this->request->param("title") . "%"];
        }

        $listRows = 10;
        $list = Db::name('industry_news')->order('id desc')->where($map)->paginate($listRows, false, ['query' => $this->request->get()]);
        $listnum = Db::name('industry_news')->field('id')->where($map)->select();
        $count = count($listnum);
        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());

        return $this->view->fetch();
    }


    /**
     * 添加操作
     *
     */
    public function add(){
        if ($this->request->isPost()) {
            try{
                $new=$this->request->post();
                $new['create_time']=time();
                $info=Db::name('industry_news')->insert($new);
                if($info)
                    return ajax_return_adv('添加成功');
                else
                    return ajax_return_adv('添加失败');
            }catch(\Exception $e){
                return ajax_return_adv_error($e->getMessage());
            }
        }else{
            return $this->view->fetch();
        }
    }

    public function edit(){
        $controller = $this->request->controller();
        if ($this->request->isPost()) {
            $id=$this->request->param('id');
            $new = $this->request->post();
            $new['update_time']=time();
            $goods = Db::name('industry_news')->where('id', $id)->update($new);
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
