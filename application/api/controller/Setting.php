<?php

namespace app\api\controller;

use app\common\controller\Api;
use fast\Http;
use \app\admin\model\light\Message as Model;
use fast\Random;
use think\Db;
use think\helper\Time;

class Setting extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    private $url = "http://139.9.31.177:13389/openapi/";
    private $getType = [
        'deviceAlarm'=>'1',//告警
        'nodeAlarm'=>'1',//告警
        'deviceStatus'=>'2',//通知
        'nodeStatus'=>'2'//通知
    ];
    private $getOnLineStatus = [
        0=>'离线',
        1=>'在线'
    ];
    private $getPowerStatuss = [
        0=>'断电',
        1=>'通电'
    ];

    private $getNodeType = [
        '01' => "30W LED电源",
        '02' => "40W LED电源",
        '05' => "50W LED电源",
        '06' => "60W LED电源",
        '11' => "70W LED电源",
        '0A' => "75W LED电源",
        '12' => "80W LED电源",
        '15' => "90W LED电源",
        '19' => "100W LED电源",
        '1D' => "120W LED电源",
        '2C' => "150W LED电源",
        '27' => "160W LED电源",
        '36' => "180W LED电源",
        '38' => "185W LED电源",
        '40' => "200W LED电源",
        '45' => "240W LED电源",
        '48' => "260W LED电源",
        '52' => "70W HID镇流器",
        '58' => "100W HID镇流器",
        '60' => "150W HID镇流器",
        '67' => "250W HID镇流器",
        '6B' => "400W HID镇流器",
        '73' => "500W 单灯控制器",
        '7A' => "500W 双灯控制器",
        '78' => "750W 单灯控制器",
        '0400' => "双色灯控制器"
    ];

    public function toRequestBody($requestParams=[])
    {
        $resParams['ver'] = "1.0";
        $resParams['client_id'] = "JyiAivhqipbfEoz1";
        $resParams['timestamp'] = time();
        $resParams['id'] = Random::uuid();
        $client_secret = "uHfvQSR9Hk8Ykgzlc7ENl1oVhmTLRE";
        $str = $resParams['client_id'] . $resParams['timestamp'] . $resParams['id'] . $client_secret;
        $resParams['sign'] = md5($str);
        $resParams['params'] = $requestParams;
        return json_encode($resParams);
    }

    public function postApi()
    {
        $pushOpen = false;
        $param = $this->request->post();
        if (!$param['method']){
            $this->error('参数不正确，请求方法不能为空！');
        }
        if (!$param['msgType']) {
            $this->error('参数不正确，msgType不能为空！');
        }
        $resBody['msgType'] = $param['msgType']?:'';
        if ($param['pushOpen'] == 1){
            $pushOpen = true;
        }
        $resBody['pushOpen'] = $pushOpen;
        $resBody['callbackUrl'] = $param['callbackUrl']?:'';
        $url = $this->url.$param['method'];
        $requestParams = $this->toRequestBody($resBody);
        $res = Http::post($url, $requestParams,  [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($requestParams)
            ]
        ]);
        $res = json_decode($res,true);
        if ($res['code'] == 200) {
            $this->success($res['msg']);
        } else {
            $this->error($res['msg']);
        }
    }
    public function setMessage()
    {
        $param = $this->request->post();

        if ($param){
            $data['msgType'] = $param['msgType'];
            $data['deviceId'] = $param['deviceId'];
            if ($param['msgType'] == 'deviceStatus' || $param['msgType'] == 'deviceAlarm'){
                $data['nodeId'] = '';
            }else{
                $data['nodeId'] = $param['nodeId'];
            }
            $data['createtime'] = $param['timestamp'];
            $data['type'] = $this->getType[$param['msgType']];

            if ($data['type'] == 1){//告警
                foreach ($param['alarm'] as $value){
                    $data['content'] = json_encode($value,true);
                    $res = Model::create($data);
                }
            }else{
                $data['content'] = json_encode($param['status'],true);
                $res = Model::create($data);
            }
            if ($res) {
                $this->success('操作成功！');
            } else {
                $this->error('操作失败，请稍后重试！');
            }
        }
    }

    public function postOpenApi(){
        $param = $this->request->post();
        $url = $this->url.$param['method'];
        $resBody['msgType'] = $param['msgType']?:'';
        $requestParams = $this->toRequestBody($resBody);
        $res = Http::post($url, $requestParams,  [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($requestParams)
            ]
        ]);
        if ($res) {
            $this->success('ok',$res);
        } else {
            $this->error('操作失败，请稍后重试！',$res);
        }
    }

    /**
     * 日志-告警列表
     */
    public function alarmList(){
        $data = Db::name('light_message')
            ->where('type','=',1)
            ->where('status','=',1)
            ->order('createtime','desc')
            ->select();

        if ($data) {
            foreach ($data as &$value){
                $content = json_decode($value['content'],true);
                $value=array_merge($value,$content);
                unset($value['content']);
            }
            $this->success('ok',$data);
        } else {
            $this->error('操作失败，请稍后重试！');
        }
    }

    /**
     * 日志-通知列表
     */
    public function statusList(){
        $data = Db::name('light_message')
            ->where('type','=',2)
            ->where('status','=',1)
            ->order('createtime','desc')
            ->select();

        if ($data) {
            foreach ($data as &$value){
                $content = json_decode($value['content'],true);
                $value=array_merge($value,$content);
                unset($value['content']);
            }
            $this->success('ok',$data);
        } else {
            $this->error('操作失败，请稍后重试！');
        }
    }

    /**
     * 首页告警数据展示
     */
    public function alarmStatistics(){
        $totalCount = Db::name('light_message')->where('type', 1)->count();
        $today = Time::today();
        $todayCount = Db::name('light_message')->where('type', 1)->where('createtime', '>=', $today[0])->count();
        $data['alarm_total'] = $totalCount?:0;
        $data['alarm_today'] = $todayCount?:0;
        if ($data) {
            $this->success('ok',$data);
        } else {
            $this->error('操作失败，请稍后重试！');
        }
    }

    /**
     * 首页告通知数据展示
     */
    public function eventInformation(){
        $data = Db::name('light_message')
            ->where('type','=',2)
            ->where('status','=',1)
            ->order('createtime','desc')
            ->select();

        if ($data) {
            foreach ($data as &$value){
                $content = json_decode($value['content'],true);
                $value=array_merge($value,$content);
                if ($value['msgType'] == 'deviceStatus'){
                    $deviceName = '集中器:'.$value['deviceId'];
                }else{
                    $deviceName = '集中器:'.$value['deviceId'].'->节点:'.$value['nodeId'];
                }
                $value['content'] = $deviceName;
            }
            $this->success('ok',$data);
        } else {
            $this->error('操作失败，请稍后重试！');
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

}