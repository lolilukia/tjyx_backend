<?php

namespace app\controllers;
use yii\web\Controller;

class SpecialController extends Controller
{
    //特色活动报名
    public function actionSign()
    {
        $member = new \app\models\Special();
        $request = \YII::$app->request;
        $act_id = $request->post('act_id');
        $stunum = $request->post('stunum');
        $name = $request->post('name');
        $college = $request->post('college');
        $gender = $request->post('gender');
        $mate_num = $request->post('mate_num');
        $mate_name = $request->post('mate_name');
        $mate_college = $request->post('mate_college');
        $mate_gender = $request->post('mate_gender');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $member->featureSign($act_id, $stunum, $name, $college, $gender, $mate_num, $mate_name, $mate_college, $mate_gender);
        echo json_encode($state);
    }

    //查询活动信息
    public function actionInfo()
    {
        $member = new \app\models\Special();
        $request = \YII::$app->request;
        $act_id = $request->get('act_id');
        $state = $member->findSpecial($act_id);
        echo json_encode($state);
    }
}