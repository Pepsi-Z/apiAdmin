<?php

namespace app\api\controller;

use app\common\controller\Api;
use \app\admin\model\light\Tunnel as Model;
use think\Db;
use think\exception\DbException;
use think\Validate;

class Tunnel extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    protected $paramArr = [
        'id'=>'ID',
        'name'=>'隧道名称',
        'code'=>'隧道编号',
        'province'=>'省份',
        'coordinates'=>'坐标',
        'start_mile'=>'起点桩号',
        'end_mile'=>'终点桩号',
        'length'=>'隧道长度',
        'direction'=>'隧道方向',
        'open_traffic'=>'通车时间',
    ];

    public function index()
    {
        try {
            $list = Model::all();
            $this->success('ok', $list);
        } catch (DbException $e) {
            $this->error($e);
        }
    }

    public function create()
    {
        $param = $this->request->post();
        $this->checkParam($param,$this->paramArr);
        $param['create_time'] = time();
        $res = Model::create($param,true);
        if ($res) {
            $this->success('保存成功！');
        } else {
            $this->error('保存失败，请稍后重试！');
        }
    }


    public function update(){
        $param = $this->request->post();
        $this->checkParam($param,$this->paramArr);
        $param['updatetime'] = time();
        $model = Model::get($param['id']);
        $res = $model->save($param);
        if ($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败，请稍后重试！');
        }

    }

    public function destroy()
    {
        $id = $this->request->post('id');
        if (!$id) {
            $this->error('参数不正确');
        }
        $res = Model::destroy($id);
        if ($res) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败，请稍后重试！');
        }
    }

    public function relation(){
        $param = $this->request->post();
        if (!$param['id']){
            $this->error('缺少参数，方案不能为空！');
        }
        if (!$param['crontab_id']) {
            $this->error('请选择定时计划');
        }
        $model = Model::get($param['id']);
        if (!$model){
            $this->error('方案不存在，请稍后重试！');
        }
        $model->crontab_id = $param['crontab_id'] ?: $model->crontab_id;
        $res = $model->save();
        if ($res){
            $this->success('定时计划绑定成功');
        }else{
            $this->error('定时计划绑定失败，请稍后重试！');
        }
    }

}