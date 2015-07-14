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

    /**
     * 输入 memberId
     * 输出 对应姓名
     * @param $memberId
     * @return string
     */
    public function getNameByMemberId($memberId)
    {
        $name = '';
        try {
            $name = Yii::app()->cnhutong->createCommand()
                ->select('name')
                ->from('ht_member')
                ->where('id = :memberId', array(':memberId' => $memberId))
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $name;
    }

    /**
     * 输入 lesson_arrange_id
     * 输出 课程对应的总课时数
     * @param $lessonArrangeId
     * @return string
     */
    public function getLessonCount($lessonArrangeId)
    {
        $lessonCount = '';
        try {
            $lessonCount = Yii::app()->cnhutong->createCommand()
                ->select('cnt')
                ->from('ht_lesson_arrange_rules')
                ->where('id = :arrangeId', array(':arrangeId' => $lessonArrangeId))
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $lessonCount;
    }

    /**
     * 输入 学员studentId, 课程排课编号lessonArrangeId
     * 输出 当前时间课时进度lesson_serial
     * @param $studentId
     * @param $lessonArrangeId
     * @return array
     */
    public function getLessonNowSerial($studentId, $lessonArrangeId)
    {
        $nowTime = date('Y-m-d');
        $lessonSerial = null;
        try {
            $lessonSerial = Yii::app()->cnhutong->createCommand()
                ->select('lesson_serial')
                ->from('ht_lesson_student')
                ->where('student_id = :studentId And lesson_arrange_id = :lessonArrangeId And date <= :nowTime',
                    array(
                        ':studentId' => $studentId,
                        ':lessonArrangeId' => $lessonArrangeId,
                        ':nowTime' => $nowTime
                    )
                )
                ->order('lesson_serial desc')
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $lessonSerial * 2;   //上一次课是2个课时
    }
}