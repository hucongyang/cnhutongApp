<?php

class ComChannel extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{com_channel}}';
    }

    public function getAppVersion()
    {
        $data = array();
        try {
            $result = Yii::app()->cnhutong_user->createCommand()
                ->select('id, last_version, new_version, download')
                ->from('com_channel')
                ->queryAll();

            // 判断数据是否为空数组
            if(ApiPublicController::array_is_null($result)) {
                $data[] = [];
            }

            foreach($result as $row) {
                $version = array();
                $version['lastVersion']               = $row['last_version'];
                $version['updateVersion']             = $row['new_version'];
                $version['downloadUrl']               = $row['download'];
                $data[]    = $version;
            }
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }
}