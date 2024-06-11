<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\admin\model\light\User;
use think\exception\DbException;
use think\Validate;

class Member extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    /**
     * 用户列表
     * @ApiMethod (POST)
     */
    public function index(){
        try {
            $list = User::where('status','=','1')->select();
            $this->success('ok', $list);
        } catch (DbException $e) {
            $this->error($e);
        }
    }

    /**
     * 删除用户
     * @ApiMethod (POST)
     * @param string $id id
     */
    public function destroy(){
        $id = $this->request->post('id');
        if (!$id) {
            $this->error('参数不正确');
        }
        if ($id == 1) {
            $this->error('初始管理员账号不能删除！');
        }
        $user = User::get($id);
        $user->status = 0;
        $res = $user->save();
        if ($res){
            $this->success('删除用户成功');
        }else{
            $this->error('删除用户失败');
        }

    }

    /**
     * 登录
     * @ApiMethod (POST)
     * @param string $account 账号
     * @param string $password 密码
     */
    public function login()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $user = User::get(['username' => $username]);
        if (!$user) {
            $this->error(__('Account is incorrect'));
        }
        if ($user->status == 0) {
            $this->error(__('Account is locked'));
        }
        if ($user->password != $this->getEncryptPassword($password)) {
            $this->error(__('Password is incorrect'));
        }
        $user = User::get($user->id);
        $ip = request()->ip();
        $user->loginip = $ip;
        $user->save();
        $data = ['userinfo' => $user->toArray()];
        $this->success(__('Logged in successful'), $data);
    }

    /**
     * 注册会员
     * @ApiMethod (POST)
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $mobile 手机号
     */
    public function register()
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        $mobile = $this->request->post('mobile');
        $name = $this->request->post('name');
        if (!$username || !$password|| !$name) {
            $this->error('参数不正确');
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error('手机格式不正确');
        }
        // 检测用户名、昵称、邮箱、手机号是否存在
        if (User::getByUsername($username)) {
            $this->error('用户名[' . $username . ']已经存在');
            return false;
        }
        if ($mobile && User::getByMobile($mobile)) {
            $this->error('手机号[' . $mobile . ']已经存在');
            return false;
        }
        $ip = request()->ip();
        $password = $this->getEncryptPassword($password);
        $params = [
            'username' => $username,
            'name' => $name,
            'password' => $password,
            'mobile' => $mobile,
            'loginip' => $ip,
            'status' => 1
        ];
        $user = User::create($params, true);
        if ($user) {
            $this->success('添加用户成功');
        } else {
            $this->error('添加用户失败');
        }
    }


    /**
     * 修改信息
     * @ApiMethod (POST)
     * @param string $id id
     * @param string $username 用户名
     * @param string $name 真实姓名
     * @param string $password 密码
     * @param string $mobile 手机号
     */
    public function profile()
    {
        $id = $this->request->post('id');
        $username = $this->request->post('username');
        $name = $this->request->post('name');
        $password = $this->request->post('password');
        $mobile = $this->request->post('mobile');
        if (!$id) {
            $this->error('参数不正确');
        }
        $user = User::get($id);
        if ($username) {
            $exists = User::where('username', $username)->where('id', '<>', $id)->find();
            if ($exists) {
                $this->error('用户名[' . $username . ']已存在');
            }
            $user->username = $username;
        }
        $password = $this->getEncryptPassword($password);
        $user->name = $name ?: $user->name;
        $user->password = $password ?: $user->password;
        $user->mobile = $mobile ?: $user->mobile;
        $res = $user->save();
        if ($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败，请稍后重试！');
        }
    }



    /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt 密码盐
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }
}