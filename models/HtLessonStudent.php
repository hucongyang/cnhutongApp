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
     * @return array
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
                date as lessonDate, time as lessonTime, student_comment')
                ->from('ht_lesson_student')
                ->where('student_id = :studentId And lesson_arrange_id = :lessonArrangeId',
                    array(
                        ':studentId' => $memberId,
                        ':lessonArrangeId' => $lessonArrangeId
                    )
                )
                ->order('lessonDate asc')
                ->queryAll();

            foreach($result as $row) {
                // 获取数据
                $lessons = array();
                $lessons['lessonStudentId']                     = $row['id'];
                $lessons['lessonSerial']                        = $row['lessonSerial'];
                $lessons['lessonDate']                          = $row['lessonDate'] . '/' . $row['lessonTime'];
                $lessons['lessonStatus']                        = self::getLessonStatus($row['step']);
                $lessons['lessonCharge']                        = $row['student_comment'];

                $data['lessons'] = $lessons;
            }
//            $data = $result;
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }


    /**
     * 获取某一次课时的具体详情
     * @param $userId                   -- 用户ID
     * @param $token                    -- 用户验证token
     * @param $memberId                 -- 用户当前绑定的学员所对应的ID
     * @param $lessonStudentId          -- 课时唯一编号
     * @return array|int
     */
    public function lessonDetails($userId, $token, $memberId, $lessonStudentId)
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
                ->select('id as lessonStudentId, lesson_serial as lessonSerial, date as lessonDate, time as lessonTime,
                step, teacher_id as teacherId, student_rating, student_comment, lesson_content')
                ->from('ht_lesson_student')
                ->where('student_id = :studentId And id = :id',
                    array(
                        ':studentId' => $memberId,
                        ':id' => $lessonStudentId
                    )
                )
                ->order('lessonDate')
                ->queryAll();

            foreach($result as $row) {
                // 获取数据
                $lessonDetail = array();
                $lessonDetail['lessonStudentId']                = $row['id'];
                $lessonDetail['lessonSerial']                   = $row['lessonSerial'];
                $lessonDetail['lessonDate']                     = $row['lessonDate'] . '' . $row['lessonTime'];
                $lessonDetail['lessonStatus']                   = self::getLessonStatus($row['step']);
                $lessonDetail['teacherId']                      = $row['teacherId'];
                $lessonDetail['teacherName']                    = ApiPublicLesson::model()->getNameByMemberId($row['teacherId']);
                $lessonDetail['lessonScore']                    = $row['student_rating'];
                $lessonDetail['lessonCharge']                   = $row['student_comment'];
                $lessonDetail['lessonContent']                  = $row['lesson_content'];
                $data['lessonDetail'] = $lessonDetail;
            }

//            $data = $result;
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }


    /**
     * 学员对上过的课时进行评价和打分
     * @param $userId
     * @param $token
     * @param $memberId
     * @param $lessonStudentId
     * @param $score
     * @param $stateComment
     * @return array|int
     */
    public function lessonStudent($userId, $token, $memberId, $lessonStudentId, $score, $stateComment)
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
                ->update('ht_lesson_student',
                    array(
                        'student_rating' => $score,
                        'student_comment' => $stateComment
                    ),
                    'student_id = :studentId And id = :id',
                    array(
                        ':studentId' => $memberId,
                        ':id' => $lessonStudentId
                    )
                );

//            $data = $result;
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }


    /**
     * 学员在APP中对自己的课时进行请假或者取消请假的操作
     * @param $userId
     * @param $token
     * @param $memberId
     * @param $lessonStudentId
     * @param $leaveType
     * @param $issue
     * @return array|int
     */
    public function lessonStudentLeave($userId, $token, $memberId, $lessonStudentId, $leaveType, $issue)
    {

    }

    /**
     * 输入 状态 step 0-8
     * 输出 对应的课时状态 正常，补课等等
     * @param $step
     * @return string
     */
    public function getLessonStatus($step)
    {
        switch ($step) {
            case "0":
                return "正常";
            case "1":
                return "补课";
            case "2":
                return "缺勤";
            case "3":
                return "弃课";
            case "4":
                return "冻结";
            case "5":
                return "退费";
            case "6":
                return "请假";
            case "7":
                return "顺延补课";
            case "8":
                return "补课后弃课";
        }
    }
}