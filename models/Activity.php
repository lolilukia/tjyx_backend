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

    public static function tableName()
    {
        return 'activity';
    }
    //判断是否可以报名
    //old
    public static function judgeAct($stuNum)
    {
        $w = date('w');
        $hours = date('H');
        $mins = date('i');
        $reg = Enroll::find()->where(['stuNum'=>$stuNum])->one();
        if(!$reg)
        {
            return Array('state'=>'noReg', 'detail'=>''); //未注册羽协
        }
        else
        {
            $user = Member::find()->where(['stuNum'=>$stuNum])->one();
            if(!$user)
            {
                return Array('state'=>'noBind', 'detail'=>'');//未绑定账号
            }
            else
            {
                if($w < 1 || $w > 4 || ($w == 1 && $hours < 17) || ($w == 3 && $hours < 17) ||
                    ($w == 2 && $hours > 20) || ($w == 2 && $hours == 20 && $mins > 30) ||
                    ($w == 4 && $hours > 20) || ($w == 4 && $hours == 20 && $mins > 30))
                {
                    return Array('state'=>'timeError', 'detail'=>'');//报名时间不对
                }
                else
                {
                    $act_time = date("Y-m-d");
                    if($w==1 || $w==3)
                    {
                        $act_time = date("Y-m-d",strtotime("+1 day"));
                    }
                    else if($w==2 || $w==4)
                    {
                        $act_time = date("Y-m-d");
                    }
                    $record = Activity::find()->where(['stuNum'=>$stuNum, 'actDate'=>$act_time])->one();
                    if(!$record)
                    {
                        return Array('state'=>'ok', 'detail'=>'');//可以报名
                    }
                    else
                    {
                        $sign_time = $record->time;
                        $total = Activity::find()->where(['<', 'time', $sign_time])->andWhere(['actDate'=>$act_time])->count();
                        return Array('state'=>'hasSign','detail'=>[$act_time, $total+1]);//已经报过名了
                    }
                }
            }
        }
    }

    //添加新的报名记录
    //old
    public static function addNewAct($stuNum)
    {
        $act = new Activity();
        $act->time = date("Y-m-d H:i:s");
        $w = date('w');
        $hours = date('H');
        $mins = date('i');
        $act_time = null;
        if($w==1 || $w==3)
        {
            $act_time = date("Y-m-d",strtotime("+1 day"));
        }
        else if($w==2 || $w==4)
        {
            $act_time = date("Y-m-d");
        }
        $act->stuNum = $stuNum;
        $act->actDate = $act_time;
        $act->period = 1;
        if($w < 1 || $w > 4 || ($w == 1 && $hours < 17) || ($w == 3 && $hours < 17) ||
            ($w == 2 && $hours > 20) || ($w == 2 && $hours == 20 && $mins > 30) ||
            ($w == 4 && $hours > 20) || ($w == 4 && $hours == 20 && $mins > 30))
        {
            return Array('state'=>'timeError', 'detail'=>'');//报名时间不对
        }
        else
        {
            $user = Member::find()->where(['stuNum'=>$stuNum])->one();
            $record = Activity::find()->where(['stuNum'=>$stuNum, 'actDate'=>$act_time])->one();
            if(!$record)    //没有相关记录
            {
                $act->save();
                $total = Activity::find()->where(['<', 'time', $act->time])->andWhere(['actDate'=>$act_time])->count();
                return Array('state'=>'success', 'detail'=>[$user->rest_time, $total+1]); //添加新记录
            }
            else
            {
                $sign_time = $record->time;
                $total = Activity::find()->where(['<', 'time', $sign_time])->andWhere(['actDate'=>$act_time])->count();
                return Array('state'=>'hasSign', 'detail'=>[$act_time, $total+1]); //已报名相同时段
            }
        }
    }

    //退报名
    //new
    public static function ModifyAct($stuNum)
    {
        $w = date('w');
        $today = date("Y-m-d");
        $act = null;
        $user = Member::find()->where(['stuNum' => $stuNum])->one();
        $rest = $user->rest_time;
        if ($w == 1 || $w == 2) {
            $rest = $user->rest_time + 3;
        }
        else if ($w == 3 || $w == 4) {
            $rest = $user->rest_time + 1;
        }
        $user->rest_time = $rest;
        $user->save();
        if($w==1 || $w==3){ //今天周一或周三报名了周二或周四的活动，删除活动记录
            $act = Activity::find()->where(['stuNum'=>$stuNum, 'actDate'=>date("Y-m-d",strtotime("+1 day"))])->one();
            if($act)
            {
                $act->delete();
                return Array('state'=>'success');
            }
        }
        else if($w==2 || $w==4){//今天周二或周四删除当天的
            $act = Activity::find()->where(['stuNum'=>$stuNum, 'actDate'=>$today])->one();
            if($act)
            {
                $act->delete();
                return Array('state'=>'success');
            }
        }
        return Array('state'=>'fail');
    }
    //返回是否报名，共多少人报名和假如报名了的具体位次信息
    //new
    public static function SignOrder($stuNum)
    {
        $w = date('w');
        $act_time = null;
        if($w==1 || $w==3)
        {
            $act_time = date("Y-m-d",strtotime("+1 day"));
        }
        else if($w==2 || $w==4)
        {
            $act_time = date("Y-m-d");
        }
        else
        {
            return Array('state'=>'success','is_sign'=>'false', 'total' => 0);
        }
        $record = Activity::find()->where(['stuNum' => $stuNum, 'actDate' => $act_time])->one();
        $all_sign = Activity::find()->where(['actDate' => $act_time])->count();
        if($record)
        {
            $total = Activity::find()->where(['<', 'time', $record->time])->andWhere(['actDate' => $act_time])->count();
            return Array('state'=>'success','is_sign'=>'true', 'total' => ($all_sign + 0), 'order'=> ($total + 1));
        }
        else
        {
            return Array('state'=>'success','is_sign'=>'false', 'total' => ($all_sign + 0));
        }
    }
    //返回当天报名人的姓名列表
    //new
    public static function Applicant($date)
    {
        $act_day = date("Y-m-d",strtotime($date));
        $applicant = Activity::find()->where(['actDate' => $act_day])->orderBy('time')->all();
        $res = Array();
        $res['count'] = count($applicant);
        for($i = 0; $i < count($applicant); $i++){
            $member = Member::find()->where(['stuNum' => $applicant[$i]['stuNum']])->one();
            $res['data'][$i]['num'] = $i + 1;
            $res['data'][$i]['name'] = $member->name;
        }
        return Array('state'=>'success', 'names'=>$res);
    }
    //添加新的报名记录
    //new
    public static function addActRecord($stuNum)
    {
        $act = new Activity();
        $act->time = date("Y-m-d H:i:s");
        $w = date('w');
        $act_time = null;
        if ($w == 1 || $w == 3) {
            $act_time = date("Y-m-d", strtotime("+1 day"));
        }
        else if ($w == 2 || $w == 4) {
            $act_time = date("Y-m-d");
        }
        $act->stuNum = $stuNum;
        $act->actDate = $act_time;
        $act->period = 1;
        $act->save();
        //判断剩余次数是否足够
        $user = Member::find()->where(['stuNum' => $stuNum])->one();
        if ($user->rest_time <= 0) {
            return Array('state' => 'insufficient'); //余额不足
        }
        else {
            $total = Activity::find()->where(['<', 'time', $act->time])->andWhere(['actDate' => $act_time])->count();
            $rest = $user->rest_time;
            if ($w == 1 || $w == 2) {
                $rest = $user->rest_time - 3;
            }
            else if ($w == 3 || $w == 4) {
                $rest = $user->rest_time - 1;
            }
            $user->rest_time = $rest;
            $user->save();
            return Array('state' => 'success', 'detail' => $total + 1); //添加新记录
        }
    }
    //查询某个用户活动记录
    //new
    public static function find_record($stunum)
    {
        $signRecords = Activity::find()->where(['stuNum'=>$stunum])->all();
        $res = Array();
        for($i = 0; $i < count($signRecords); $i++) {
            $res[$i]['time'] = substr($signRecords[$i]->actDate, 0, 10);
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
}