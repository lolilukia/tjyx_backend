<?php

namespace app\models;
use yii\db\ActiveRecord;

class Survey extends ActiveRecord
{
    public static function tableName()
    {
        return 'survey';
    }
    //判断是否可以填写调查问卷
    //new
    public static function judgeSurvey($stuNum)
    {
        $record = Survey::find()->where(['stuNum'=>$stuNum])->one();
        if($record)
        {
            return Array('survey'=> 1);
        }
        else
        {
            return Array('survey'=> 0);
        }
    }

    //将调查问卷的记录存储到表中
    //new
    public static function addSurvey($stuNum, $yearsexp, $purpose, $willing, $hope, $advise)
    {
        $psq = new Survey();
        $psq->stuNum = $stuNum;
        $psq->yearsexp = $yearsexp;
        $psq->purpose = $purpose;
        $psq->willing = $willing;
        $psq->hope = $hope;
        $psq->advise = $advise;
        $psq->save();
        return Array('state'=>'success');
    }
}
