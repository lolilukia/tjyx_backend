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

    public static function recharge($stunum, $pwd, $amount)
    {
        //预留接口，如需要设定为某管理员才有充值的权限
        $cwNum = '1652610';
        $setting = 'jdyxCz18';
        $cw_num = Member::find()->where(['stuNum'=>$cwNum])->one();
        if($cw_num){
            if($pwd == $setting){
                $member = Member::find()->where(['stuNum'=>$stunum])->one();
                if(!$member){
                    return Array('state'=>'fail');
                }
                else{
                    $ori = $member->rest_time;
                    $member->rest_time = $ori + ($amount / 3);
                    $member->save();
                    return Array('state'=>'success');
                }
            }
            else{
                return Array('state'=>'denied');
            }
        }
        else{
            return Array('state'=>'cw_noBind');
        }
    }
}
