<?php
/**
 * Created by PhpStorm.
 * User: Li_Jing
 * Date: 2018/3/7
 * Time: 17:12
 */

namespace app\controllers;
use yii\web\Controller;

class SurveyController extends Controller
{
    //新增调查问卷信息
    //new
    public function actionSurvey()  //调查问卷方法
    {
        $survey = new \app\models\Survey();
        $request = \YII::$app->request;
        $number = $request->post('stunum');
        $year = $request->post('yearsexp');
        $pur = $request->post('purpose');
        $will = $request->post('willing');
        $want = $request->post('hope');
        $suggestion = $request->post('advise');
        header("Access-Control-Allow-Origin: *");//同源策略 跨域请求 头设置
        header('content-type:text/html;charset=utf8 ');
        $state = $survey->addSurvey($number, $year, $pur, $will, $want, $suggestion);
        echo json_encode($state);
    }

    //判断是否填写过问卷
    //new
    public function actionInvestigate()
    {
        $survey = new \app\models\Survey();
        $request = \YII::$app->request;
        $number = $request->get('stunum');
        $state = $survey->judgeSurvey($number);
        echo json_encode($state);
    }
}