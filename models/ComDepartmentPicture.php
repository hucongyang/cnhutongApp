<?php

class ComDepartmentPicture extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{com_department_picture}}';
    }

    /**
     * 获取校区信息的具体信息
     * @param $departmentId
     * @return array|bool
     */
    public function getSchoolInfoPicture($departmentId)
    {
        $pictures = array();
        try {
            $pictures_model = Yii::app()->cnhutong_user->createCommand()
                ->select('picture, desc')
                ->from('com_department_picture')
                ->where('department_id = :departmentId', array(':departmentId' => $departmentId))
                ->queryAll();
            if(!$pictures_model) {
                return false;
            }
            foreach($pictures_model as $row) {
                $pictures[] = array(
                    'picUrl' => $row['picture'],
                    'picInfo' => $row['desc']
                );
            }
        } catch (Exception $e) {
            error_log($e);
        }
        return $pictures;
    }
}