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
    public static function featureSign($act_id, $stuNum, $name, $college, $gender, $mate_num, $mate_name, $mate_college, $mate_gender)
    {
        $record = Special::find()->where(['act_id' => $act_id, 'stuNum' => $stuNum])->one();
        $other = Special::find()->where(['act_id' => $act_id, 'mateNum' => $stuNum])->one();
        if($record || $other){
            return Array('state' => 'signed');
        }
        else{
            $feature = new Special();
            $feature->act_id = $act_id;
            $feature->stuNum = $stuNum;
            $feature->name = $name;
            $feature->college = $college;
            $feature->gender = $gender;
            $feature->mateNum = $mate_num;
            $feature->mateName = $mate_name;
            $feature->mateCollege = $mate_college;
            $feature->mateGender = $mate_gender;
            $feature->save();
            return Array('state' => 'success');
        }
    }

    //返回特色活动报名人数
    public static function findSpecial($act_id)
    {
        $act_info = Special::find()->orderBy('sps_id')->all();
        $res = Array();
        $gender = ['男', '女'];
        for($i = 0; $i < count($act_info); $i++) {
            $res['data'][$i]['name'] = $act_info[$i]->name;
            $res['data'][$i]['gender'] = $gender[$act_info[$i]->gender];
            $res['data'][$i]['mname'] = $act_info[$i]->mateName;
            $res['data'][$i]['mgender'] = $gender[$act_info[$i]->mateGender];
        }
        return Array('state' => 'success', 'info' => $res);
    }
}