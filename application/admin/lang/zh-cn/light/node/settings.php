<?php

return [
    'Id'               => 'ID',
    'Deviceid'         => '集中器设备序列号',
    'Nodeid'           => '节点设备序列号（指定配置单个节点时该值必填,否则对集中器下的所有节点配置相同阈值）',
    'Highleakcurrent'  => '节点漏电流上限阈值（单位：mA）,取值范围：5~100mA',
    'Highinputvoltage' => '节点输入电压上限阈值（单位：V）,取值范围：200~300V',
    'Lowinputvoltage'  => '节点输入电压下限阈值（单位：V）,取值范围：80~180V',
    'Createtime'       => '创建时间',
    'Updatetime'       => '更新时间'
];
