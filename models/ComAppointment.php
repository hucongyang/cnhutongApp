<?php

class ComAppointment extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{com_appointment}}';
    }

    /**
     * 用户在App中提交试听等相关信息
     * @param $userId
     * @param $name
     * @param $mobile
     * @param $subject
     * @return string
     */
    public function postAppointment($userId, $name, $mobile, $subject)
    {
        $result =  0;
        try {
            $result = Yii::app()->cnhutong_user->createCommand()
                ->insert('com_appointment',
                    array(
                        'user_id' => $userId,
                        'name' => $name,
                        'mobile' => $mobile,
                        'subject' => $subject
                    )
                );
        } catch (Exception $e) {
            error_log($e);
        }
        return $result;
    }

    public function getCommonInfo()
    {
        $result = array();
        $ads = array();
        try {

        } catch (Exception $e) {
            error_log($e);
        }
        return $result;
    }
}