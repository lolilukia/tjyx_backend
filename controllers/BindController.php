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
        $name = $request->post('realname');
        $number = $request->post('stunum');
        $nickname = $request->post('nickname');
        $success = Array('state'=>'success');
        $fail = Array('state'=>'no register');
        $fail_1 = Array('state'=>'no openid');
        $fail_2 = Array('state'=>'already bind');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $user->bindUser($code, $name, $number, $nickname);
        if($state==1){
            echo json_encode($success);
        }
        else if($state==2){
            echo json_encode($fail);
        }
        else if($state==3){
            echo json_encode($fail_1);
        }
        else if($state==4){
            echo json_encode($fail_2);
        }
    }
}