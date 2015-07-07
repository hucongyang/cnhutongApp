<?php

class SmsSendHistory extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{sms_send_history}}';
    }
}