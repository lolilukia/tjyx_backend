<?php
/**
 * Created by PhpStorm.
 * Enroll: Li_Jing
 * Date: 2017/10/28
 * Time: 13:20
 */

namespace app\controllers;
use yii\web\Controller;
include('../models/Enroll.php');

class BindController extends Controller
{
    public function actionAdd()
    {
        $user = new \app\models\Enroll();
        $request = \YII::$app->request;
        $name = $request->post('realname');
        $number = $request->post('stunum');
        $college = $request->post('college');
        $phone = $request->post('phoneNum');
        $willing = $request->post('willing');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $user->addNewUser($name, $number, $college, $phone, $willing);
        echo json_encode($state);
    }

    public function actionBind()
    {
        $user = new \app\models\Enroll();
        $request = \YII::$app->request;
        $code = $request->post('code');
        $stuNum = $request->post('stunum');
        $nickname = $request->post('nickname');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $user->bindUser($code, $stuNum, $nickname);
        echo json_encode($state);
    }
}