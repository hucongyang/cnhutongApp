<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/8
 * Time: 10:10
 */

class HtLessonStudent extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{ht_lesson_student}}';
    }

    /**
     * 获取学员所有正在进行课程的下一次上课时间
     * @param $userId       -- 用户ID
     * @param $token        -- 用户验证token
     * @param $memberId     -- 用户当前绑定的学员所对应的ID
     * @return array
     */
    public function nextLessonList($userId, $token, $memberId)
    {
        $data = array();
        $nowTime = date('Y-m-d');
        try {
            // 用户ID验证
            $isUserId = User::IsUserId($userId);
            if(!$isUserId) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 用户token验证
            $isToken = UserToken::IsToken($userId, $token);
            if(!$isToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }

            $result = Yii::app()->cnhutong->createCommand()
                ->select('course_id, lesson_arrange_id as lessonArrangeId, id as lessonStudentId,
                date as lessonDate, time as lessonTime, lesson_serial as lessonSerial,
                department_id as departmentId')
                ->from('ht_lesson_student')
                ->where('student_id = :studentId And date >= :beginDate',
                    array(
                        ':studentId' => $memberId,
                        ':beginDate' => $nowTime
                    )
                )
//                ->group('course_id')
                ->order('lessonSerial desc')
                ->queryAll();

            foreach($result as $row) {
                // 获取数据
                $lesson = array();

                $lesson['courseId']                          = $row['course_id'];
                $subjectId = ApiPublicLesson::model()->getSubjectIdByCourseId($lesson['courseId'] );
                $lesson['subjectId']                         = $subjectId;
                $lesson['lessonArrangeId']                  = $row['lessonArrangeId'];
                $lesson['lessonStudentId']                  = $row['lessonStudentId'];
                $subjectInfo = ApiPublicLesson::model()->getSubjectInfoById($subjectId);
                $lesson['subjectName']                       = $subjectInfo['title'];
                $lesson['subjectPic']                        = $subjectInfo['feature_img'];
                $lesson['lessonSerial']                      = $row['lessonSerial'];
                $lesson['lessonDate']                        = $row['lessonDate'] . ' ' . $row['lessonTime'];
                $lesson['departmentId']                      = $row['departmentId'];
                $departmentInfo = ApiPublicLesson::model()->getDepartmentInfoById($lesson['departmentId']);
                $lesson['departmentName']                    = $departmentInfo['name'];
                $data['lesson'] = $lesson;
            }

//            $data = $result;
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }

    /**
     * 获取学员所有的课程，包括正在学习的课程和历史课程
     * @param $userId       -- 用户ID
     * @param $token        -- 用户验证token
     * @param $memberId     -- 用户当前绑定的学员所对应的ID
     * @return array
     */
    public function allSubjects($userId, $token, $memberId)
    {
        $data = array();
        try {
            // 用户ID验证
            $isUserId = User::IsUserId($userId);
            if(!$isUserId) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 用户token验证
            $isToken = UserToken::IsToken($userId, $token);
            if(!$isToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }

            $result = Yii::app()->cnhutong->createCommand()
                ->select('id, course_id, lesson_arrange_id as lessonArrangeId, teacher_id as teacherId,
                lesson_serial as lessonSerial,
                department_id as departmentId')
                ->from('ht_lesson_student')
                ->where('student_id = :studentId',
                    array(
                        ':studentId' => $memberId
                    )
                )
                ->queryAll();

            foreach($result as $row) {
                // 获取数据
                $subject = array();
                $subject['id']                                 = $row['id'];
                $subject['courseId']                          = $row['course_id'];
                $subjectId = ApiPublicLesson::model()->getSubjectIdByCourseId($subject['courseId'] );
                $subject['subjectId']                         = $subjectId;
                $subject['lessonArrangeId']                  = $row['lessonArrangeId'];
                $subjectInfo = ApiPublicLesson::model()->getSubjectInfoById($subjectId);
                $subject['subjectName']                       = $subjectInfo['title'];
                $subject['teacherId']                           = $row['teacherId'];
                $subject['teacherName']                         = ApiPublicLesson::model()->getNameByMemberId($row['teacherId']);
                $subject['lessonSerial']                      = $row['lessonSerial'];
                $lessonCount = ApiPublicLesson::model()->getLessonCount($subject['lessonArrangeId']);
                $subject['lessonProcess']                      = $row['lessonSerial'] . '/' . $lessonCount;
                $subject['lessonStatus']                      = $row['lessonSerial'];
                $subject['departmentId']                      = $row['departmentId'];
                $departmentInfo = ApiPublicLesson::model()->getDepartmentInfoById($subject['departmentId']);
                $subject['departmentName']                    = $departmentInfo['name'];
                $data['subject'] = $subject;
            }

//            $data = $result;
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }

    /**
     * 获取学员指定课程的具体课时详情（课程表）
     * @param $userId       -- 用户ID
     * @param $token        -- 用户验证token
     * @param $memberId     -- 用户当前绑定的学员所对应的ID
     * @param $lessonArrangeId      -- 课程的唯一排课编号
     */
    public function subjectSchedule($userId, $token, $memberId, $lessonArrangeId)
    {
        $data = array();
        try {
            // 用户ID验证
            $isUserId = User::IsUserId($userId);
            if(!$isUserId) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 用户token验证
            $isToken = UserToken::IsToken($userId, $token);
            if(!$isToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }

            $result = Yii::app()->cnhutong->createCommand()
                ->select('id as lessonStudentId, lesson_serial as lessonSerial,
                date as lessonDate, step')
                ->from('ht_lesson_student')
                ->where('student_id = :studentId And lesson_arrange_id = :lessonStudentId',
                    array(
                        ':studentId' => $memberId,
                        ':lessonStudentId' => $lessonArrangeId
                    )
                )
                ->order('lessonDate asc')
                ->queryAll();
            $aStep = array(
                '0' => '正常',
                '1' => '补课',
                '2' => '缺勤',
                '3' => '弃课',
                '4' => '冻结',
                '5' => '退费',
                '6' => '请假',
                '7' => '顺延补课',
                '8' => '补课后弃课'
            );
            $data = $result;
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }
}