<?php
namespace app\common\model;

use think\Model;

class LogMoney extends Model
{
    // 指定表名,不含前缀
    protected $name = 'log_expend';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    //获取车辆类型
    public static function getMoneyType(){
        return   $moneyType = [
            [
                'id'   => 0 ,
                'name' => '收入'
            ] ,
            [
                'id'   => 1 ,
                'name' => '支出'
            ]
        ];
    }
}
