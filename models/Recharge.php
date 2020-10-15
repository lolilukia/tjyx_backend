<?php
/**
 * Created by PhpStorm.
 * User: Li_Jing
 * Date: 2018/5/11
 * Time: 00:45
 */

namespace app\models;
use yii\db\ActiveRecord;

class Recharge extends ActiveRecord
{
    public static function tableName()
    {
        return 'recharge';
    }
    //充值为用户添加次数，并添加到充值记录中
    //new
    public static function recharge($stunum, $pwd, $amount)
    {
        //预留接口，如需要设定为某管理员才有充值的权限
        $cwNum = '1610831';
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
                    $r = new Recharge();
                    $r->stuNum = $stunum;
                    $r->recharge_time = date("Y-m-d");
                    $r->action_time = date("Y-m-d H:i:s");
                    $r->amount = $amount;
                    $r->times = $amount/3;
                    $r->save();
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
    //查询某个用户的充值记录
    //new
    public static function find_recharge($stunum)
    {
        $rechargeRecords = Recharge::find()->where(['stuNum'=>$stunum])->all();
        $res = Array();
        for($i = 0; $i < count($rechargeRecords); $i++) {
            $res[$i]['time'] = substr($rechargeRecords[$i]->recharge_time, 0, 10);
            $res[$i]['amount'] = $rechargeRecords[$i]->amount;
            $res[$i]['number'] = $rechargeRecords[$i]->times;
        }
        return Array('count'=>count($rechargeRecords), 'records'=>$res);
    }
}