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
    public static function ModifyAct($stuNum)
    {
        $w = date('w');
        $today = date("Y-m-d");
        $act = null;
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
}