<?php
namespace app\admin\controller;


use app\admin\Controller;
use think\Db;
use think\exception\HttpException;
use think\Exception;

class StageSet extends Controller
{

    /**
     * 佣金比例显示
     */
    public  function index(){
        $list= Db::name('stage_set')->find();
        $this->view->assign('list',$list);
        return $this->view->fetch();
    }

    /**
     * 关于我们添加
     *
     */
    public function add(){
        $controller = $this->request->controller();
        if($this->request->isPost()){
            //写入数据
            try{
                $new=$this->request->post();
                $list= Db::name('stage_set')->insert($new);
                if($list){
                    return ajax_return_adv('添加成功','current');
                }else{
                    return ajax_return_adv('添加失败','current');
                }
            }catch(\Exception $e){
                return ajax_return_adv_error($e->getMessage());
            }
        }else{
            $id = $this->request->param('id');
            $info = $this->getModel($controller)->find($id);
            $this->view->assign('list',$info);
            return  $this->view->fetch('admin/stage_set/index');
        }
    }

    /**
     * 关于我们编辑
     */
    public function edit(){
        $id=$this->request->param('id');
        if ($this->request->isAjax()) {
            $data = $this->request->post();
            $goods = Db::name('stage_set')->where('id', $id)->update($data);
            if ($goods) {
                return ajax_return_adv('修改成功','current');
            } else {
                return ajax_return_adv('修改失败','current');
            }
        } else {
            //获取到该数据的值
            if (!$id) {
                throw new Exception("缺少参数ID");
            }
            $vo =  Db::name('stage_set')->where('id', $id)->find();
            if (!$vo) {
                throw new HttpException(404, '该记录不存在');
            }
            $this->view->assign('list', $vo);

            return $this->view->fetch();
        }
    }




}
