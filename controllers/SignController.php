<?php
/**
 * Created by PhpStorm.
 * User: Li_Jing
 * Date: 2018/1/26
 * Time: 17:12
 */

namespace app\controllers;
use yii\web\Controller;

class SignController extends Controller
{
    public function actionSign()  //签到-old
    {
        $sign = new \app\models\Signup();
        $request = \YII::$app->request;
        $number = $request->post('stunum');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $sign->UserSignup($number);
        echo json_encode($state);
    }

    public function actionFind() //查询活动记录-new
    {
        $sign = new \app\models\Signup();
        $request = \YII::$app->request;
        $number = $request->get('stunum');
        $state = $sign->find_record($number);
        echo json_encode($state);
    }

    public function actionAdd()  //签到-new
    {
        $sign = new \app\models\Signup();
        $request = \YII::$app->request;
        $number = $request->post('stunum');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $sign->signUp($number);
        echo json_encode($state);
    }
}