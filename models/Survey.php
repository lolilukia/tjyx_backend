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
        $record = Survey::find()->where(['stuNum' => $stuNum])->one();
        if($record)
        {
            $year = $record->yearsexp;
            $purpose = $record->purpose;
            $willing = $record->willing;
            $hope = $record->hope;
            $advise = $record->advise;
            return Array('survey' => 1, 'year' => $year, 'purpose' => $purpose,
                'willing' => $willing, 'hope' => $hope, 'advise' => $advise);
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
        $record = Survey::find()->where(['stuNum' => $stuNum])->one();
        if($record){
            if($record->yearsexp != $yearsexp) $record->yearsexp = $yearsexp;
            if($record->purpose != $purpose) $record->purpose = $purpose;
            if($record->willing != $willing) $record->willing = $willing;
            if($record->hope != $hope) $record->hope = $hope;
            if($record->advise != $advise) $record->advise = $advise;
            $record->save();
            return Array('state' => 'update');
        }
        else{
            $psq = new Survey();
            $psq->stuNum = $stuNum;
            $psq->yearsexp = $yearsexp;
            $psq->purpose = $purpose;
            $psq->willing = $willing;
            $psq->hope = $hope;
            $psq->advise = $advise;
            $psq->save();
            return Array('state' => 'add');
        }
    }
}
