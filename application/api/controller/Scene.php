<?php

namespace app\api\controller;

use app\common\controller\Api;
use \app\admin\model\light\Scene as Model;
use think\Db;
use think\exception\DbException;
use think\Validate;

class Scene extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function index()
    {
        try {
            $list = Db::table('yx_light_scene')->where('deletetime',null)->select();
            $crontabList = Db::table('yx_light_scheme')->where('deletetime',null)->select();
            foreach ($list as $key => $value){
                foreach ($crontabList as $v){
                    if (str_contains($value['scheme_ids'], $v['id'])) {
                        $list[$key]['schemeList'][] =$v;
                    }
                }
            }
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
        if (!$param['scheme_ids']) {
            $this->error('参数不正确,请选择定控制方案！');
        }
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
            $this->error('参数不正确，ID不能为空！');
        }
        if (!$param['title']) {
            $this->error('参数不正确，标题不能为空！');
        }
        if (!$param['scheme_ids']) {
            $this->error('参数不正确，请选择定控制方案！');
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
            $this->error('参数不正确，ID不能为空！');
        }
        $res = Model::destroy($id);
        if ($res) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败，请稍后重试！');
        }
    }


}