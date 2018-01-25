<?php
/**
 * Created by PhpStorm.
 * User: Li_Jing
 * Date: 2017/11/1
 * Time: 16:23
 */

namespace app\models;
use yii\db\ActiveRecord;

class Signup extends ActiveRecord
{
    public $signId;
    public $stuNum;
    public $time;
    public $duration;

    public static function tableName()
    {
        return 'signup';
    }

    //已报名当天活动进行签到
    public static function UserSignup($stuNum)
    {
        $sign = new Signup();
        $sign->stuNum = $stuNum;
        $sign->time = date("Y-m-d");
        $record = Activity::find()->where(['stuNum'=>$stuNum, 'actDate'=>$sign->time])->one();
        if(!$record){
            return 2; //未报名活动
        }
        else{
            $sign->duration = $record->period;
            $sign->save();
            return 1; //签到成功
        }
    }
}