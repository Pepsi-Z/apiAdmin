<?php

namespace app\api\controller;

use app\common\controller\Api;
use \app\admin\model\light\device\Settings as DeviceModel;
use \app\admin\model\light\node\Settings as NodeModel;
use think\Db;
use think\exception\DbException;
use think\Validate;

class Alerts extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function index()
    {
        try {
            $param = $this->request->post();
            if (!$param['deviceId']) {
                $this->error('参数不正确，集中器ID不能为空！');
            }
            if (!empty($param['nodeId'])){
                $list = Db::name('light_node_settings')
                    ->where('deviceId','=',$param['deviceId'])
                    ->where('nodeId','=',$param['nodeId'])
                    ->find();
            }else{
                $list = Db::name('light_device_settings')->where('deviceId','=',$param['deviceId'])->find();
            }
            $this->success('ok', $list);

        } catch (DbException $e) {
            $this->error($e);
        }
    }

    public function create()
    {
        $param = $this->request->post();
        if (!empty($param['nodeId'])){
            $res = NodeModel::create($param,true);
        }else{
            $res = DeviceModel::create($param,true);
        }
        if ($res) {
            $this->success('保存成功！');
        } else {
            $this->error('保存失败，请稍后重试！');
        }
    }


    public function deviceUpdate(){
        $param = $this->request->post();
        if (!$param['id']){
            $this->error('缺少参数，ID不能为空！');
        }
        $model = DeviceModel::get($param['id']);
        $res = $model->save($param);
        if ($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败，请稍后重试！');
        }

    }

    public function nodeUpdate(){
        $param = $this->request->post();
        if (!$param['id']){
            $this->error('缺少参数，ID不能为空！');
        }
        $model = NodeModel::get($param['id']);
        $res = $model->save($param);
        if ($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败，请稍后重试！');
        }

    }

}