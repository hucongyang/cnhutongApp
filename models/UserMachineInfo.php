<?php

class UserMachineInfo extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{user_machine_info}}';
    }

    /**
     * 输入：$device_id  用户注册时的设备ID
     * 输出：$userId     用户App唯一 ID
     * @param $deviceId
     * @return string
     */
    public function IsExistUserIdByDeviceId($deviceId)
    {
        $userId = '';
        try {
            $userId = Yii::app()->cnhutong_user->createCommand()
                ->select('user_id')
                ->from('user_machine_info')
                ->where('device_id = :deviceId', array(':deviceId' => $deviceId))
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $userId;
    }
}