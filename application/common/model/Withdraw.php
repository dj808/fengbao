<?php
namespace app\common\model;

use think\Model;

class Withdraw extends Model
{
    // 指定表名,不含前缀
    protected $name = 'withdraw';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    //获取提现状态类型
    public function getWithdrawStatus(){
        return   $statusType = [
            [
                'id'   => 0 ,
                'name' => '待提现'
            ] ,
            [
                'id'   => 1 ,
                'name' => '发放成功'
            ] ,
            [
                'id'   => 2 ,
                'name' => '提现失败'
            ]
        ];
    }
}
