<?php

namespace app\controllers;
use yii\web\Controller;

class CoachController extends Controller
{
    //判断是否为教练并是否签到
    //new
    public function actionJudge()
    {
        $student = new \app\models\Coach();
        $request = \YII::$app->request;
        $number = $request->get('stunum');
        $state = $student->coach_record($number);
        echo json_encode($state);
    }

    //教练签到
    //new
    public function actionSign()
    {
        $student = new \app\models\Coach();
        $request = \YII::$app->request;
        $number = $request->post('stunum');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $student->coach_sign($number);
        echo json_encode($state);
    }
}