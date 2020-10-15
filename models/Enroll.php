<?php

namespace app\models;
use yii\db\ActiveRecord;
include('Member.php');

class Enroll extends ActiveRecord
{
    public static function tableName()
    {
        return 'enroll';
    }

    //招新添加新的会员
    //old
    public static function addNewUser($name, $stuNum, $college, $phoneNum, $willStaff)
    {
        $customer = new Enroll();
        $record = Enroll::find()->where(['stuNum'=>$stuNum])->one();
        if($record)
        {
            return Array('state'=>'fail');
        }
        else
        {
            $customer->stuNum = $stuNum;
            $customer->name = $name;
            $customer->college = $college;
            $customer->phoneNum = $phoneNum;
            $customer->willingStaff = $willStaff;
            $customer->save();
            return Array('state'=>'success');
        }
    }
    //获取用户唯一标识openid
    //old
    public static function getOpenid($code)
    {
        $app_id = 'wx284a40da3abb10cf';
        $app_secret = '36598fdc5aa85dbec8a96a5413d023b5';
        $open_url = 'https://api.weixin.qq.com/sns/jscode2session';
        $data = http_build_query(array('appid'=>$app_id, 'secret'=>$app_secret, 'js_code'=>$code, 'grant_type'=>'authorization_code'));
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'content-type:text/html;charset=utf8',
                'content' => $data,
                'timeout' => 300 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $res = file_get_contents($open_url, false, $context);
        $object = json_decode($res);
        if ($object->{'openid'}){
            return $object->{'openid'};
        }
        else{
            return NULL;
        }
    }

    /*
    public static function setEncode($text)
    {
        $data = NULL;
        $e=mb_detect_encoding($text, array('UTF-8', 'GBK', 'gb2312'));
        switch($e){
            case 'UTF-8' : //如果是utf8编码就直接插入数据库
                $data = $text;
                break;
            case 'GBK': //如果是gbk编码就转换为utf-8之后再插入数据库
                $data = mb_convert_encoding($text, "UTF-8", "GBK");
                break;
            case 'GB2312': //如果是GB2312编码就转换为utf-8之后再插入数据库
                $data = mb_convert_encoding($text, "UTF-8", "GB2312");
                break;
        }
        return $data;
    }*/

    //判断绑定账号的是否为会员
    //old
    public static function bindUser($code, $stuNum, $nickname)
    {
        $custom_record = Enroll::find()->where(['stuNum'=>$stuNum])->one();
        if(!$custom_record){
            return Array('state'=>'no register'); //未注册
        }
        else{
            $name = $custom_record->name;
            $student = Member::find()->where(['stuNum'=>$stuNum])->one();
            if($student)
            {
                return Array('state'=>'already bind'); //已绑定
            }
            else
            {
                $enroll = new Enroll();
                $open_id = $enroll->getOpenid($code);
                if(!$open_id)
                {
                    return Array('state'=>'no openid'); //网络原因未能获取用户标示
                }
                else
                {
                    Member::addMember($name, 10, 0, $open_id, $stuNum, $nickname);
                    return Array('state'=>'success'); //绑定账号成功
                }
            }
        }
    }
    //查询用户的个人信息
    //new
    public static function search_info($stuNum)
    {
        $user = Enroll::find()->where(['stuNum'=>$stuNum])->one();
        if($user)
        {
            return Array('state'=> 'success', 'name' => $user->name, 'college' => $user->college, 'phone' => $user->phoneNum, 'willing' => $user->willingStaff, 'coach' => $user->coach);
        }
        else
        {
            return Array('state'=> 'fail');
        }
    }
    //更新用户的信息
    //new
    public static function updateInfo($name, $stuNum, $college, $phoneNum, $willStaff)
    {
        $customer = new Enroll();
        $record = Enroll::find()->where(['stuNum'=>$stuNum])->one();
        if($record)
        {
            $record->name = $name;
            $record->college = $college;
            $record->phoneNum = $phoneNum;
            $record->willingStaff = $willStaff;
            $record->save();
            return Array('state'=>'update_success');
        }
        else
        {
            $customer->stuNum = $stuNum;
            $customer->name = $name;
            $customer->college = $college;
            $customer->phoneNum = $phoneNum;
            $customer->willingStaff = $willStaff;
            $customer->save();
            Member::addMember($name, 0, 0, null, $stuNum, null);
            return Array('state'=>'add_success');
        }
    }
}
