<?php
namespace app\common\model;

use think\Model;

class LogInvite extends Model
{
    // 指定表名,不含前缀
    protected $name = 'log_invite';

    public static function getStatusType(){
        return   $statusType = [
            [
                'id'   => 0 ,
                'name' => '未邀请到 '
            ] ,
            [
                'id'   => 1 ,
                'name' => '已邀请到 '
            ]
        ];
    }
    
}
