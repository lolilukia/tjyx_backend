<?php

namespace app\models;
use yii\db\ActiveRecord;

class Enroll extends ActiveRecord
{
    public static function tableName()
    {
        return 'enroll';
    }

    //招新添加新的会员
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
    }

    //判断绑定账号的是否为会员
    public static function bindUser($code, $name, $stuNum, $nickname)
    {
        $customer = new Enroll();
        $stuNum = $customer->setEncode($stuNum);
        $customer->stuNum = $stuNum;
        $name = $customer->setEncode($name);
        $customer->name = $name;
        $code = $customer->setEncode($code);
        $nickname = $customer->setEncode($nickname);
        $customer = Enroll::find()->where(['stuNum'=>$stuNum])->one();
        if(!$customer){
            return 2; //未注册
        }
        else{
            $student = Member::find()->where(['stuNum'=>$stuNum])->one();
            if($student)
            {
                return 4; //已绑定
            }
            else
            {
                $open_id = $customer->getOpenid($code);
                if(!$open_id)
                {
                    return 3; //网络原因未能获取用户标示
                }
                else
                {
                    Member::addMember($name, 10, 0, $open_id, $stuNum, $nickname);
                    return 1; //绑定账号成功
                }
            }
        }
    }
}
