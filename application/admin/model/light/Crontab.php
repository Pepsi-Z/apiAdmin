<?php

namespace app\admin\model\light;

use think\Model;


class Crontab extends Model
{

    

    

    // 表名
    protected $name = 'light_crontab';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;


}
