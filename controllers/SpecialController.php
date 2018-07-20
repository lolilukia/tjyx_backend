<?php

namespace app\controllers;
use yii\web\Controller;

class SpecialController extends Controller
{
    //特色活动报名
    //new
    public function actionSign()
    {
        $member = new \app\models\Special();
        $request = \YII::$app->request;
        $act_id = $request->post('act_id');
        $name = $request->post('name');
        $college = $request->post('college');
        $phone = $request->post('phone');
        $mate_name = $request->post('mate_name');
        $mate_college = $request->post('mate_college');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $member->feature_sign($act_id, $name, $college, $phone, $mate_name, $mate_college);
        echo json_encode($state);
    }
}