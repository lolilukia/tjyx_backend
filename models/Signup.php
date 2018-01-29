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

    public static function tableName()
    {
        return 'signup';
    }

    //已报名当天活动进行签到
    public static function UserSignup($stuNum)
    {
        $sign = new Signup();
        $sign->stuNum = $stuNum;
        $w = date('w');
        $hours = date('H');
        $mins = date('i');
        $sign->time = null;
        if($w == 2 || $w == 4)
        {
            $sign->time = date("Y-m-d");
        }
        else
        {
            $sign->time = date("Y-m-d",strtotime("+1 day"));
        }
        //判断是否签到过
        $signRecord = Signup::find()->where(['stuNum'=>$stuNum, 'time'=>$sign->time])->one();
        if($signRecord)
        {
            return Array('state'=>'hasCheck');
        }
        else
        {
            //判断是否报名
            $record = Activity::find()->where(['stuNum'=>$stuNum, 'actDate'=>$sign->time])->one();
            if(!$record){
                return Array('state'=>'noSign'); //未报名活动
            }
            else
            {
                //判断签到时间，在当天下午五点半后
                if(($w != 2 && $w != 4) || (($w == 2 || $w == 4) && $hours < 17) || (($w == 2 || $w == 4) && $hours == 17 && $mins < 30))
                {
                    return Array('state'=>'signTimeError');
                }
                else
                {
                    //判断剩余次数是否足够
                    $user = Member::find()->where(['stuNum'=>$stuNum])->one();
                    if($user->rest_time <= 0)
                    {
                        return Array('state'=>'insufficient'); //余额不足
                    }
                    else
                    {
                        $total = Activity::findBySql("select (select count(*)+1 from activity where time < b.time) as number from activity b where b.stuNum =:stuNum;",array(':stuNum'=>$stuNum));
                        //是否在30名之内
                        if($total < 30){
                            $sign->duration = 1;
                            $rest = $sign->rest_time - 3;
                            $sign->rest_time = $rest;
                            $sign->save();
                            return Array('state'=>'success'); //签到成功
                        }
                        else
                        {
                            return Array('state'=>'orderError');//排序在30以外
                        }
                    }
                }
            }
        }
    }
}