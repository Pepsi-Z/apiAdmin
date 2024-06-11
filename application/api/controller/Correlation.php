<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\exception\DbException;
use \app\admin\model\light\Correlation as Model;
use think\Validate;

class Correlation extends Api
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
        $type = $this->request->post('type');
        $model_id = $this->request->post('model_id');
        $concentrator_id = $this->request->post('concentrator_id');
        $group_id = $this->request->post('group_id');
        $node_id = $this->request->post('node_id');
        if (!$type || !$model_id) {
            $this->error('参数不正确');
        }
        if (Model::getByModel_id($model_id)) {
            $this->error('此模型已经被绑定，不可新建关系！');
        }
        $param['type'] = $type;
        $param['model_id'] = $model_id;
        $param['node_id'] = $node_id?:'';
        $param['group_id'] = $group_id?:'';
        $param['concentrator_id'] = $concentrator_id?:'';
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
        if (!$param['type']) {
            $this->error('绑定类型不能为空');
        }
        if (!$param['model_id']) {
            $this->error('模型ID不能为空');
        }
        $model = Model::get($param['id']);
        $model->type = $param['type'] ?: $model->type;
        $model->model_id = $param['model_id'] ?: $model->model_id;
        $model->concentrator_id = $param['concentrator_id'] ?: $model->concentrator_id;
        $model->group_id = $param['group_id'] ?: $model->group_id;
        $model->node_id = $param['node_id'] ?: $model->node_id;
        $model->save();
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
            $this->success('解绑成功！');
        } else {
            $this->error('解绑失败，请稍后重试！');
        }
    }

    public function renewal(){
        $param = $this->request->post();

        if ($param){
            Db::startTrans();
            try {
                foreach ($param as &$subArray) {
                    $subArray['createtime'] = time(); // 使用当前时间作为 createtime
                    $subArray['updatetime'] = time(); // 使用当前时间作为 updatetime
                }
                Db::name('light_correlation')->where('1=1')->delete();
                $res = Db::name('light_correlation')->insertAll($param);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($res) {
                $this->success('操作成功！');
            } else {
                $this->error('操作失败，请稍后重试！');
            }
        }
    }

}