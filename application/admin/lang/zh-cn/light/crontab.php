<?php

return [
    'Id'            => 'ID',
    'Title'         => '标题',
    'Begindate'     => '任务开始日期（格式：yyyy-MM-dd）',
    'Enddate'       => '任务结束日期（格式：yyyy-MM-dd）',
    'Actionschema'  => '执行模式：1单次执行,2每天执行,3每周执行',
    'Actionweekday' => '执行星期：0~6对应星期日~星期六',
    'Computermode'  => '计算模式：0固定时间,1日出时间,2日落时间,3光控模式',
    'Executetime'   => '执行时间（格式：HH:mm:ss）,时间模式为固定时间时才有值',
    'Offsettime'    => '修正值：日出日落偏移时间,取值范围 -128 ~ 127（单位：
分钟）',
    'Crontab_id'    => '定时计划ID'
];
