<?php

namespace app\api\controller;

use app\common\controller\Api;
use \app\admin\model\light\Crontab as Model;
use think\Db;
use think\exception\DbException;
use think\Validate;

class Crontab extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function index()
    {
        try {
            $list = Db::table('yx_light_crontab')
                ->where('yx_light_scheme.deletetime',null)
                ->join('yx_light_scheme','yx_light_crontab.scheme_id = yx_light_scheme.id','LEFT')
                ->field('yx_light_crontab.*,yx_light_scheme.title as s_title,deviceId,jobsNum,controlType,radio,groupNum,ab,luminance')
                ->select();
            $this->success('ok', $list);
        } catch (DbException $e) {
            $this->error($e);
        }
    }

    public function create()
    {
        $param = $this->request->post();
        if (!$param['title']) {
            $this->error('参数不正确，标题不能为空！');
        }
        if (!$param['beginDate'] || !$param['endDate']) {
            $this->error('参数不正确');
        }
        if($param['actionSchema'] == '3' && empty($param['actionWeekDay'])){
            $this->error('每周执行需要设置执行星期！');
        }
        if($param['computerMode'] == '0' && !$param['executeTime']){
            $this->error('固定时间模式需要设置执行时间！');
        }
        $param['create_time'] = time();
//        dump($param);die;
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
        if (!$param['beginDate'] || !$param['endDate']) {
            $this->error('参数不正确');
        }
        if($param['actionSchema'] == '3' && empty($param['actionWeekDay'])){
            $this->error('每周执行需要设置执行星期！');
        }
        if($param['computerMode'] == '0' && !$param['executeTime']){
            $this->error('固定时间模式需要设置执行时间！');
        }
        $model = Model::get($param['id']);
        $res = $model->save($param);
        if ($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败，请稍后重试！');
        }

    }
    public function updateStatus(){
        $param = $this->request->post();
        if (!$param['id']){
            $this->error('参数不正确，ID不能为空！');
        }
        if (!$param['status']) {
            $this->error('参数不正确，下发状态不能为空！');
        }
        $model = Model::get($param['id']);
        $model->status = $param['status'] ?: $model->status;
        $res = $model->save();
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


}