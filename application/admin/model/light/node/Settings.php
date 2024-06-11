<?php

namespace app\admin\model\light\node;

use think\Model;


class Settings extends Model
{

    

    

    // 表名
    protected $name = 'light_node_settings';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
