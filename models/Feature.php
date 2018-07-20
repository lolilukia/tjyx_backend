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
    public static function getInfo()
    {
        $act_info = Feature::find()->orderBy('act_id')->all();
        $res = Array();
        for($i = 0; $i < count($act_info); $i++) {
            $res[$i]['act_id'] = $act_info[$i]->act_id;
            $res[$i]['name'] = $act_info[$i]->name;
            $res[$i]['act_time'] = substr($act_info[$i]->act_time, 0, 10);
        }
        return Array('state'=> 'success', 'activity'=>$res);
    }

    //获取某个特色活动的信息
    public static function getDetail($id)
    {
        $act_info = Feature::find()->where(['act_id'=>$id])->one();
        $time = substr($act_info->act_time, 0, 10);
        return Array('state'=> 'success', 'name'=>$act_info->name, 'act_time'=>$time, 'sign_time'=>
        $act_info->sign_time, 'finished'=>$act_info->finished, 'detail'=>$act_info->detail, 'url'=>
        $act_info->url);
    }

}
