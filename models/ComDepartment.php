<?php

class ComDepartment extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{com_department}}';
    }

    /**
     * 获取所有校区的列表以及基本内容
     * @return array|bool
     */
    public static function getAllSchools()
    {
        $data = array();
        $school = array();
        $school_models = Yii::app()->cnhutong_user->createCommand()
            ->select('id, name, telphone, address, point_x, point_y, picture')
            ->from('com_department')
            ->queryAll();
        if(!$school_models) {
            return false;
        }
        foreach($school_models as $row)
        {
            $school[] = array(
                'departmentId' => $row['id'],
                'name' => $row['name'],
                'telphone' => $row['telphone'],
                'address' => $row['address'],
                'pointX' => $row['point_x'],
                'point_y' => $row['point_y'],
                'phone' => $row['picture']
            );
            $data['schools'] = $school;
        }
        return $data;
    }

    /**
     * 获得校区具体信息
     * @param $departmentId
     * @return array|bool
     */
    public static function getSchoolInfo($departmentId)
    {
        $data = array();
        $school_model = Yii::app()->cnhutong_user->createCommand()
            ->select('id, name, bus_line, parking')
            ->from('com_department')
            ->where('id = :departmentId', array(':departmentId' => $departmentId))
            ->queryRow();
        if(!$school_model) {
            return false;
        }
        $data[] = array(
            'id' => $school_model['id'],
            'name' => $school_model['name'],
            'busLine' => $school_model['bus_line'],
            'parking' => $school_model['parking']
        );
        return $data;
    }
}