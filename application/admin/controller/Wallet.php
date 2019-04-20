<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;
use app\common\model\Wallet as WalletModel;

class Wallet extends Controller
{
    use \app\admin\traits\controller\Controller;


    /**
     * 列表
     */
    public function index(){
        //根据条件查询
        $map=[];
        $map['b.isdelete']=0;
        if ($this->request->param("username")) {
            $userinfo=Db::name('user')->field('id')->where('nickname','like',"%" . $this->request->param("username") . "%")->select();
             foreach ($userinfo as $value){
                 $map['b.id']=$value['id'];
             }
        }
        

        $listRows = 10;
        $list = Db::name('wallet')
            ->alias('a')
            ->field('a.*,b.nickname,money,score')
            ->join('__USER__ b ','a.user_id=b.id')
            ->order('a.id desc')
            ->where($map)
            ->paginate($listRows, false, ['query' => $this->request->get()]);
        $listnum =$list->toArray();
        $count = intval($listnum['total']);

        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());

        $model=new WalletModel();
        $withdrawStatus=$model->getStatusType();
        $this->view->assign('withdrawStatus',$withdrawStatus);
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
                $new['isdelete']=0;
                $info = Db::name('wallet')->insert($new);
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
            $model=new WalletModel();
            $withdrawStatus=$model->getStatusType();

            $this->view->assign('withdrawStatus',$withdrawStatus);
            return $this->view->fetch();

        }

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
            $goods = Db::name('wallet')->where('id', $id)->update($data);
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
            $list =  Db::name('wallet')->where('id', $id)->find();
            if (!$list) {
                throw new HttpException(404, '该记录不存在');
            }
            $this->view->assign('list', $list);
            $model=new WalletModel();
            $withdrawStatus=$model->getStatusType();
            $this->view->assign('withdrawStatus',$withdrawStatus);
            return $this->view->fetch();

        }
    }
}
