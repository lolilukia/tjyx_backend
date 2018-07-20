<?php

namespace app\models;
use yii\db\ActiveRecord;

class Special extends ActiveRecord
{
    public static function tableName()
    {
        return 'special';
    }

    //特色活动报名
    public static function feature_sign($act_id, $name, $college, $phone, $mate_name, $mate_college)
    {
        $feature = new Special();
        $feature->act_id = $act_id;
        $feature->name = $name;
        $feature->college = $college;
        $feature->phoneNum = $phone;
        $feature->matename = $mate_name;
        $feature->matecollege = $mate_college;
        $feature->save();
        return Array('state' => 'success');
    }
}