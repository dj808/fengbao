<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;

class Banner extends Controller
{
    use \app\admin\traits\controller\Controller;


    public function add(){
        if ($this->request->isPost()) {
            try{
                $new=$this->request->post();
                $new['create_time']=time();
                $info=Db::name('banner')->insert($new);
                if($info)
                    return ajax_return_adv('添加成功');
                else
                    return ajax_return_adv('添加失败');
            }catch(\Exception $e){
                return ajax_return_adv_error($e->getMessage());
            }
        }else{
            return $this->view->fetch(isset($this->template) ? $this->template : 'add');

        }
    }


    public function edit(){
        $controller = $this->request->controller();
        if ($this->request->isPost()) {
            $id=$this->request->param('id');
            $new = $this->request->post();
            $goods = Db::name('banner')->where('id', $id)->update($new);
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

    
}
