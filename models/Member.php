<?php

namespace app\models;
use yii\db\ActiveRecord;

class Member extends ActiveRecord
{
    public static function tableName()
    {
        return 'member';
    }

    //绑定账号后添加到会员表中
    public static function addMember($name, $rest_time, $integral, $openid, $stuNum, $nickname)
    {
        $stu = new Member();
        $stu->name = $name;
        $stu->rest_time = $rest_time;
        $stu->integral = $integral;
        $stu->openid = $openid;
        $stu->stuNum = $stuNum;
        $stu->nickname = $nickname;
        $stu->save();
        return true;
    }
}
