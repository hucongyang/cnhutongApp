<?php

class ComAds extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{com_ads}}';
    }

    public function getAds()
    {
        $data = array();
        try {
            $result = Yii::app()->cnhutong_user->createCommand()
                ->select('id, picture, text, link')
                ->from('com_ads')
                ->queryAll();

            // 判断数据是否为空数组
            if(ApiPublicController::array_is_null($result)) {
                $data[] = [];
            }

            foreach($result as $row) {
                $ads = array();
                $ads['adPicture']               = $row['picture'];
                $ads['adContent']               = $row['text'];
                $ads['adUrl']                    = $row['link'];
                $data[]    = $ads;
            }
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }
}