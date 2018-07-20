<?php
/**
 * Created by PhpStorm.
 * User: Li_Jing
 * Date: 2018/5/9
 * Time: 20:45
 */

namespace app\controllers;
use yii\web\Controller;
include('../models/Member.php');

class InfoController extends Controller
{
    public function actionTime()
    {
        $user = new \app\models\Member();
        $request = \YII::$app->request;
        $number = $request->get('stunum');
        //header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        //header('content-type:text/html;charset=utf8 ');
        $state = $user->rest_time($number);
        echo json_encode($state);
    }

}