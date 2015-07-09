<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/8
 * Time: 11:16
 */
class ApiPublicLesson extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 输入课程ID（course_id）
     * 输出课程ID (subjectId)
     * @param $courseId
     * @return string
     */
    public function getSubjectIdByCourseId($courseId)
    {
        $subjectId = '';
        try {
            $subjectId = Yii::app()->cnhutong->createCommand()
                ->select('subject_id')
                ->from('ht_course')
                ->where('id = :courseId', array(':courseId' => $courseId))
                ->queryScalar();
            if(!$subjectId) {
                $subjectId = '';
            }
        } catch (Exception $e) {
            error_log($e);
        }
        return $subjectId;
    }

    /**
     * 输入课程类别ID （subjectId）
     * 输出课程信息 （subjectInfo）
     * @param $subjectId
     * @return array
     */
    public function getSubjectInfoById($subjectId)
    {
        $subjectInfo = array();
        try {
            $subjectInfo = Yii::app()->cnhutong->createCommand()
                ->select('id, title, description, feature_img')
                ->from('ht_subject')
                ->where('id = :subjectId', array(':subjectId' => $subjectId))
                ->queryRow();
        } catch (Exception $e) {
            error_log($e);
        }
        return $subjectInfo;
    }

    /**
     * 输入校区编号（departmentId）
     * 输出校区信息 (departmentInfo)
     * @param $departmentId
     * @return array
     */
    public function getDepartmentInfoById($departmentId)
    {
        $departmentInfo = array();
        try {
            $departmentInfo = Yii::app()->cnhutong->createCommand()
                ->select('id, name, department, region, province, city, district')
                ->from('ht_department')
                ->where('id = :departmentId', array(':departmentId' => $departmentId))
                ->queryRow();
        } catch (Exception $e) {
            error_log($e);
        }
        return $departmentInfo;
    }
}