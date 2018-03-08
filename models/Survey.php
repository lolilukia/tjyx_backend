<?php

namespace app\models;
use yii\db\ActiveRecord;

class Survey extends ActiveRecord
{
    public static function tableName()
    {
        return 'survey';
    }

    //将调查问卷的记录存储到表中
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
