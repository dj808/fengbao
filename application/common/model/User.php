<?php
namespace app\common\model;

use think\Model;

class User extends Model
{
    // 指定表名,不含前缀
    protected $name = 'user';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    public function getCircleInfo(){
        return $this->hasOne('Circle','user_id');
    }

    public function getUserOpintion(){
        return $this->hasMany('Opintion','user_id','id')->field('nickname');
    }
    

    
    
    
}
