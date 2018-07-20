<?php

namespace app\models;
use yii\db\ActiveRecord;

class Coach extends ActiveRecord
{
    public static function tableName()
    {
        return 'coach';
    }
    //判断是否是教练并且是否签到
    //new
    public static function coach_record($stunum)
    {
        $member = Enroll::find()->where(['stuNum' => $stunum])->one();
        if($member->coach == 1)
        {
            $act_time = date("Y-m-d");
            $is_sign = Coach::find()->where(['stuNum' => $stunum, 'time' => $act_time])->one();
            if(!$is_sign)
            {
                return Array('state' => 'success', 'coach' => 1, 'sign' => 0);
            }
            else
            {
                return Array('state' => 'success', 'coach' => 1, 'sign' => 1);
            }
        }
        else
        {
            return Array('state' => 'success', 'coach' => 0);
        }
    }
    //教练签到
    //new
    public static function coach_sign($stunum)
    {
        $student = new Coach();
        $student->stunum = $stunum;
        $student->time = date("Y-m-d");
        $student->save();
        return Array('state' => 'success');
    }
}