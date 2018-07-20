<?php

namespace app\controllers;
use yii\web\Controller;

class FeatureController extends Controller
{
    //获取所有特色活动的信息
    //new
    public function actionFind()
    {
        $feature = new \app\models\Feature();
        $state = $feature->getInfo();
        echo json_encode($state);
    }

    //获取某个特色活动的信息
    //new
    public function actionDetail()
    {
        $feature = new \app\models\Feature();
        $request = \YII::$app->request;
        $number = $request->get('id');
        $state = $feature->getDetail($number);
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