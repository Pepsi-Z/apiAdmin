<?php

namespace app\api\controller;

use app\common\controller\Api;
use \app\admin\model\light\Scheme as Model;
use think\Db;
use think\exception\DbException;
use think\Validate;

class Scheme extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

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
        if (!$param['deviceId'] || !$param['radio']) {
            $this->error('参数不正确');
        }
        if($param['radio'] == '1' && !$param['groupNum']){
            $this->error('组播模式需要选中分组！');
        }
        if (!$param['title']) {
            $this->error('参数不正确，标题不能为空！');
        }
        $deviceNum = Model::where('deviceId','=',$param['deviceId'])->MAX('jobsNum');
        $param['jobsNum'] = $deviceNum + 1;
        $param['controlType'] = 2;
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

        if (!$param['id']){
            $this->error('缺少参数，ID不能为空！');
        }
        if (!$param['deviceId'] || !$param['radio']) {
            $this->error('参数不正确');
        }
        if($param['radio'] == '1' && !$param['groupNum']){
            $this->error('组播模式需要选中分组！');
        }
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