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
    //old
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
                        $sign_time = $record->time;
                        $total = Activity::find()->where(['<', 'time', $sign_time])->andWhere(['actDate'=>$sign->time])->count();
                        //是否在30名之内
                        if($total < 30){
                            $sign->duration = 1;
                            $sign->weekday = $w;
                            $rest = $user->rest_time;
                            if($w == 2){
                                $rest = $user->rest_time - 3;
                            }
                            else if($w == 4){
                                $rest = $user->rest_time - 1;
                            }
                            $user->rest_time = $rest;
                            $user->save();
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
    //查询某个用户活动（签到）的记录
    //new
    public static function find_record($stunum)
    {
        $signRecords = Signup::find()->where(['stuNum'=>$stunum])->all();
        $res = Array();
        for($i = 0; $i < count($signRecords); $i++) {
            $res[$i]['time'] = substr($signRecords[$i]->time, 0, 10);
            if($signRecords[$i]->weekday == 2){
                $res[$i]['weekday'] = '教学场';
                $res[$i]['number'] = 3;
            }
            else{
                $res[$i]['weekday'] = '自由活动场';
                $res[$i]['number'] = 1;
            }
        }
        return Array('count'=>count($signRecords), 'records'=>$res);
    }
    //已报名当天活动进行签到
    //new
    public static function signUp($stuNum)
    {
        $sign = new Signup();
        $sign->stuNum = $stuNum;
        $w = date('w');
        $sign->time = date("Y-m-d");
        //判断剩余次数是否足够
        $user = Member::find()->where(['stuNum'=>$stuNum])->one();
        if($user->rest_time <= 0)
        {
            return Array('state'=>'insufficient'); //余额不足
        }
        else
        {
            $record = Activity::find()->where(['stuNum'=>$stuNum, 'actDate'=>$sign->time])->one();
            $sign_time = $record->time;
            $total = Activity::find()->where(['<', 'time', $sign_time])->andWhere(['actDate'=>$sign->time])->count();
            //是否在30名之内
            if($total < 30){
                $sign->duration = 1;
                $sign->weekday = $w;
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