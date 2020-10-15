<?php
/**
 * Created by PhpStorm.
 * User: Li_Jing
 * Date: 2017/11/1
 * Time: 13:58
 */

namespace app\models;
use yii\db\ActiveRecord;

class Activity extends ActiveRecord
{
    public static $teach_weekday = 2;
    public static $act_weekday = 2;
    public static $teach_start_time = "15:00:00";
    public static $act_start_time = "17:30:00";
    public static $teach_num = 3;
    public static $act_num = 1;

    public static function tableName()
    {
        return 'activity';
    }
    //退报名
    //new
    public static function ModifyAct($stuNum, $type)
    {
        $w = date('w');
        $today = date("Y-m-d");
        $act = null;
        $user = Member::find()->where(['stuNum' => $stuNum])->one();
        $rest = $user->rest_time;
        $time = null;
        if($type == 0) $time = self::$teach_start_time;
        else $time = self::$act_start_time;
        if($w == (self::$teach_weekday - 1) || $w == (self::$act_weekday - 1)){ //前一天报名的，删除活动记录
            $act = Activity::find()->where(['stuNum' => $stuNum, 'actDate' => date("Y-m-d",strtotime("+1 day")), 'actTime' => $time])->one();
            if($act)
            {
                if($type == 0){
                    $rest = $user->rest_time + 3;
                }
                else{
                    $rest = $user->rest_time + 1;
                }
                $act->delete();
                $user->rest_time = $rest;
                $user->save();
                return Array('state' => 'success');
            }
        }
        else if($w == self::$teach_weekday || $w == self::$act_weekday){//删除当天的
            $act = Activity::find()->where(['stuNum' => $stuNum, 'actDate' => $today, 'actTime' => $time])->one();
            $hours = date('H');
            $teach_hour = intval(explode(':', self::$teach_start_time)[0]);
            $act_hour = intval(explode(':', self::$act_start_time)[0]);
            if($act && (($type == 0 && $hours < $teach_hour)||($type == 1 && $hours < $act_hour)))
            {
                if($type == 0){
                    $rest = $user->rest_time + 3;
                }
                else{
                    $rest = $user->rest_time + 1;
                }
                $act->delete();
                $user->rest_time = $rest;
                $user->save();
                return Array('state' => 'success');
            }
        }
        return Array('state'=>'fail');
    }
    //返回是否报名，共多少人报名和假如报名了的具体位次信息
    //new
    public static function SignOrder($stuNum, $type)
    {
        $w = date('w');
        if($type == 0) $act_time = self::$teach_start_time;
        else $act_time = self::$act_start_time;
        $act_date = null;
        if($w == (self::$teach_weekday - 1) || $w == (self::$act_weekday - 1))
        {
            $act_date = date("Y-m-d",strtotime("+1 day"));
        }
        else if($w == self::$teach_weekday || $w == self::$act_weekday)
        {
            $act_date = date("Y-m-d");
        }
        else
        {
            return Array('state' => 'success','is_sign' => 'false', 'total' => 0);
        }
        $record = Activity::find()->where(['stuNum' => $stuNum, 'actDate' => $act_date, 'actTime' => $act_time])->one();
        $all_sign = Activity::find()->where(['actDate' => $act_date, 'actTime' => $act_time])->count();
        if($record)
        {
            $total = Activity::find()->where(['<', 'time', $record->time])->andWhere(['actDate' => $act_date, 'actTime' => $act_time])->count();
            return Array('state'=>'success','is_sign'=>'true', 'total' => ($all_sign + 0), 'order'=> ($total + 1));
        }
        else
        {
            return Array('state' => 'success','is_sign' => 'false', 'total' => ($all_sign + 0));
        }
    }
    //返回当天报名人的姓名列表
    //new
    public static function Applicant($date, $type)
    {
        $act_day = date("Y-m-d",strtotime($date));
        $act_time = null;
        if($type == 0) $act_time = self::$teach_start_time;
        else $act_time = self::$act_start_time;
        $applicant = Activity::find()->where(['actDate' => $act_day, 'actTime' => $act_time])->orderBy('time')->all();
        $res = Array();
        $res['count'] = count($applicant);
        for($i = 0; $i < count($applicant); $i++){
            $member = Member::find()->where(['stuNum' => $applicant[$i]['stuNum']])->one();
            $res['data'][$i]['num'] = $i + 1;
            $res['data'][$i]['name'] = $member->name;
        }
        return Array('state' => 'success', 'names' => $res);
    }
    //添加新的报名记录
    //new
    public static function addActRecord($stuNum, $type)
    {
        $w = date('w');
        $act_date = null;
        if ($w == (self::$teach_weekday - 1) || $w == (self::$act_weekday - 1)) {
            $act_date = date("Y-m-d", strtotime("+1 day"));
        }
        else if ($w == self::$teach_weekday || $w == self::$act_weekday) {
            $act_date = date("Y-m-d");
        }
        $act_time = null;
        if($type == 0) $act_time = self::$teach_start_time;
        else $act_time = self::$act_start_time;
        $record = Activity::find()->where(['stuNum' => $stuNum, 'actDate' => $act_date, 'time' => $act_time])->one();
        if(!$record){
            $act = new Activity();
            $act->time = date("Y-m-d H:i:s");
            $act->stuNum = $stuNum;
            $act->actDate = $act_date;
            $act->actTime = $act_time;
            if($type == 0) $act->period = 0;
            else $act->period = 1;
            //判断剩余次数是否足够
            $user = Member::find()->where(['stuNum' => $stuNum])->one();
            if (($type == 0 && $user->rest_time < 3)||($type == 1 && $user->rest_time < 1)) {
                return Array('state' => 'insufficient'); //余额不足
            }
            else {
                $total = Activity::find()->where(['<', 'time', $act->time])->andWhere(['actDate' => $act_date, 'actTime' => $act_time])->count();
                $rest = $user->rest_time;
                if ($type == 0) {
                    $rest = $user->rest_time - 3;
                }
                else {
                    $rest = $user->rest_time - 1;
                }
                $act->save();
                $user->rest_time = $rest;
                $user->save();
                return Array('state' => 'success', 'detail' => $total + 1); //添加新记录
            }
        }
        else{
            $info = Activity::find()->where(['stuNum' => $stuNum, 'actDate' => $act_date, 'actTime' => $act_time])->one();
            $total = Activity::find()->where(['<', 'time', $info->time])->andWhere(['actDate' => $act_time, 'actTime' => $act_time])->count();
            return Array('state' => 'success', 'detail' => $total + 1);
        }
    }
    //查询某个用户活动记录
    //new
    public static function find_record($stuNum)
    {
        $signRecords = Activity::find()->where(['stuNum' => $stuNum])->all();
        $res = Array();
        for($i = 0; $i < count($signRecords); $i++) {
            $res[$i]['date'] = $signRecords[$i]->actDate;
            $res[$i]['time'] = $signRecords[$i]->actTime;
            if($signRecords[$i]->actTime == self::$teach_start_time){
                $res[$i]['weekday'] = '教学场';
                $res[$i]['number'] = self::$teach_num;
            }
            else{
                $res[$i]['weekday'] = '自由活动场';
                $res[$i]['number'] = self::$act_num;
            }
        }
        return Array('count' => count($signRecords), 'records' => $res);
    }
}