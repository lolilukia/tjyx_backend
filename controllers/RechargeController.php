<?php
/**
 * Created by PhpStorm.
 * User: Li_Jing
 * Date: 2018/3/10
 * Time: 16:05
 */

namespace app\controllers;
use yii\web\Controller;
include('../models/Recharge.php');

class RechargeController extends Controller
{
    public function actionPay()
    {
        $user = new \app\models\Recharge();
        $request = \YII::$app->request;
        $stunum = $request->post('stunum');
        $pwd = $request->post('pwd');
        $amount = $request->post('amount');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $user->recharge($stunum, $pwd, $amount);
        echo json_encode($state);
    }

    public function actionFind()
    {
        $user = new \app\models\Recharge();
        $request = \YII::$app->request;
        $stunum = $request->get('stunum');
        //header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        //header('content-type:text/html;charset=utf8 ');
        $state = $user->find_recharge($stunum);
        echo json_encode($state);
    }
}