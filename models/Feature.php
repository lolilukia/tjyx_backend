<?php

namespace app\models;
use yii\db\ActiveRecord;

class Feature extends ActiveRecord
{
    public static function tableName()
    {
        return 'feature';
    }

    //获取所有特色活动的信息
    public static function getInfo($stuNum)
    {
        $act_info = Feature::find()->orderBy('act_id')->all();
        $res = Array();
        for($i = 0; $i < count($act_info); $i++) {
            $res[$i]['act_id'] = $act_info[$i]->act_id;
            $res[$i]['name'] = $act_info[$i]->name;
            $res[$i]['act_time'] = $act_info[$i]->act_time;
            $res[$i]['sign_time'] = $act_info[$i]->sign_time;
            $record = Special::find()->where(['act_id' => $act_info[$i]->act_id, 'stuNum' => $stuNum])->one();
            $other = Special::find()->where(['act_id' => $act_info[$i]->act_id, 'mateNum' => $stuNum])->one();
            if($record || $other) $res[$i]['sign_state'] = '已报名';
            else $res[$i]['sign_state'] = '未报名';
            $res[$i]['detail'] = $act_info[$i]->detail;
        }
        return Array('state' => 'success', 'activity' => $res);
    }

    //获取某个特色活动的信息
    public static function getDetail($id, $stuNum)
    {
        $act_info = Feature::find()->where(['act_id' => $id])->one();
        $time = $act_info->act_time;
        $record = Special::find()->where(['act_id' => $id, 'stuNum' => $stuNum])->one();
        $other = Special::find()->where(['act_id' => $id, 'mateNum' => $stuNum])->one();
        if($record || $other){
            return Array('state' => 'success', 'sign' => 1, 'name' => $act_info->name, 'act_time' => $time, 'sign_time' =>
                $act_info->sign_time, 'finished' => $act_info->finished, 'detail' => $act_info->detail, 'url' =>
                $act_info->url);
        }
        else{
            return Array('state' => 'success', 'sign' => 0, 'name' => $act_info->name, 'act_time' => $time, 'sign_time' =>
                $act_info->sign_time, 'finished' => $act_info->finished, 'detail' => $act_info->detail, 'url' =>
                $act_info->url);
        }
    }
}
