<?php
/**
 * Created by PhpStorm.
 * User: Li_Jing
 * Date: 2017/11/1
 * Time: 13:56
 */

namespace app\controllers;
use yii\web\Controller;

class ActivityController extends Controller
{
    public function actionAct()  //新增报名信息-old
    {
        $act = new \app\models\Activity();
        $request = \YII::$app->request;
        $number = $request->post('stunum');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $act->addNewAct($number);
        echo json_encode($state);
    }

    public function actionJudge()//判断是否能报名-old
    {
        $act = new \app\models\Activity();
        $request = \YII::$app->request;
        $number = $request->post('stunum');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $act->judgeAct($number);
        echo json_encode($state);
    }

    public function actionCancel() //退报名-new
    {
        $act = new \app\models\Activity();
        $request = \YII::$app->request;
        $number = $request->post('stunum');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $act->ModifyAct($number);
        echo json_encode($state);
    }

    public function actionOrder() //查询报名情况及报名位次及报名总人数-new
    {
        $act = new \app\models\Activity();
        $request = \YII::$app->request;
        $number = $request->get('stunum');
        $state = $act->SignOrder($number);
        echo json_encode($state);
    }

    public function actionApplicant() //查询具体报名的同学-new
    {
        $act = new \app\models\Activity();
        $request = \YII::$app->request;
        $number = $request->get('date');
        $state = $act->Applicant($number);
        echo json_encode($state);
    }

    public function actionAdd()  //新增报名信息-new
    {
        $act = new \app\models\Activity();
        $request = \YII::$app->request;
        $number = $request->post('stunum');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $act->addActRecord($number);
        echo json_encode($state);
    }

    public function actionFind() //查询活动记录-new
    {
        $sign = new \app\models\Activity();
        $request = \YII::$app->request;
        $number = $request->get('stunum');
        $state = $sign->find_record($number);
        echo json_encode($state);
    }

}