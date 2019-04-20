<?php
namespace app\admin\controller;

\think\Loader::import('controller/Controller', \think\Config::get('traits_path') , EXT);

use app\admin\Controller;
use think\Db;

class User extends Controller
{
    use \app\admin\traits\controller\Controller;
    // 方法黑名单
    protected static $blacklist = [];

    //过滤方法
    protected function filter(&$map)
    {
        if ($this->request->param("realname")) {
            $map['realname'] = ["like", "%" . $this->request->param("realname") . "%"];
        }
        if ($this->request->param("nickname")) {
            $map['nickname'] = ["like", "%" . $this->request->param("nickname") . "%"];
        }
        if ($this->request->param("mobile")) {
            $map['nickname'] = ["like", "%" . $this->request->param("mobile") . "%"];
        }
    }
    /**
     * 列表
     */
    public function index(){
        //根据条件查询
        $map=[];
        $map['isdelete']=0;
        if ($this->request->param("realname")) {
            $map['realname'] = ["like", "%" . $this->request->param("realname") . "%"];
        }
        if ($this->request->param("nickname")) {
            $map['nickname'] = ["like", "%" . $this->request->param("nickname") . "%"];
        }
        if ($this->request->param("phone")) {
            $map['phone'] = ["like", "%" . $this->request->param("phone") . "%"];
        }

        $listRows = 10;
        $list = Db::name('user')
              ->where($map)
              ->order('id desc')
              ->paginate($listRows, false, ['query' => $this->request->get()]);

        $listnum = Db::name('user')->field('id')->where($map)->select();
        $count = count($listnum);
        $this->view->assign('list', $list);
        $this->view->assign("count", $count);
        $this->view->assign("page", $list->render());

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
                $info = Db::name('user')->insert($new);
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
            $data = $this->request->param('status');
            $goods = Db::name('user')->where('id', $id)->setField('status',$data);
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
            $vo =  Db::name('user')->where('id', $id)->find();
            if (!$vo) {
                throw new HttpException(404, '该记录不存在');
            }
            $this->view->assign('list', $vo);

            return $this->view->fetch();
        }
    }

    public function detail(){
        $id=$this->request->param('id');

        $list=Db::name('user')
            ->alias('a')
            ->field('a.*,b.name as industry_name')
            ->join('__INDUSTRY__ b','a.industry_id=b.id')
         //   ->join('__BANK__ c','a.bank_id=c.id')
            ->order('a.id desc')
            ->where('a.id',$id)
            ->find();

        $this->view->assign('list', $list);
        return $this->view->fetch();
    }

}
