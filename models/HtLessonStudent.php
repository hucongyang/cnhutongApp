<?php

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
            $user = User::model()->IsUserId($userId);
            if(!$user) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 用户token验证
            $userToken = UserToken::model()->IsToken($userId, $token);
//            var_dump($userToken);exit;
            if(!$userToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }

            $isExistUserMemberId = UserMember::model()->IsExistMemberId($userId, $memberId);
            if(!$isExistUserMemberId) {
                return 40003;        // MSG_ERR_FAIL_MEMBER
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
                ->group('course_id')
                ->order('lessonSerial asc')
                ->queryAll();

            // 判断数据是否为空数组
            if(ApiPublicController::array_is_null($result)) {
                $data[] = [];
            }

//            if(empty($result)) {
//                $data[] = [];
//            }

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
                $data[] = $lesson;
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
            $user = User::model()->IsUserId($userId);
            if(!$user) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 用户token验证
            $userToken = UserToken::model()->IsToken($userId, $token);
//            var_dump($userToken);exit;
            if(!$userToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }

            $isExistUserMemberId = UserMember::model()->IsExistMemberId($userId, $memberId);
            if(!$isExistUserMemberId) {
                return 40003;        // MSG_ERR_FAIL_MEMBER
            }

            $result = Yii::app()->cnhutong->createCommand()
                ->select('id, course_id, teacher_id, cnt, department_id')
                ->from('ht_lesson_arrange_rules')
                ->where('student_id = :studentId',
                    array(
                        ':studentId' => $memberId
                    )
                )
                ->queryAll();

            // 判断数据是否为空数组
            if(ApiPublicController::array_is_null($result)) {
                $data[] = [];
            }

            foreach($result as $row) {
                // 获取数据
                $subject = array();
                $subjectId = ApiPublicLesson::model()->getSubjectIdByCourseId($row['course_id']);
                $subject['subjectId']                         = $subjectId;
                $subject['lessonArrangeId']                  = $row['id'];
                $subjectInfo = ApiPublicLesson::model()->getSubjectInfoById($subjectId);
                $subject['subjectName']                       = $subjectInfo['title'];
                $subject['teacherId']                           = $row['teacher_id'];
                $subject['teacherName']                         = ApiPublicLesson::model()->getNameByMemberId($row['teacher_id']);
                $cnt                                             = $row['cnt'];
                $lessonNowSerial = ApiPublicLesson::model()->getLessonNowSerial($memberId, $row['id']);
                $subject['lessonProgress']                      = $lessonNowSerial . '/' . $row['cnt'];
                if($lessonNowSerial < $cnt) {
                    $subject['lessonStatus']                        = '进行中';
                } elseif ($lessonNowSerial == $cnt) {
                    $subject['lessonStatus']                        = '已完成';
                } else {
                    $subject['lessonStatus']                        = '';
                }
                $subject['departmentId']                      = $row['department_id'];
                $departmentInfo = ApiPublicLesson::model()->getDepartmentInfoById($subject['departmentId']);
                $subject['departmentName']                    = $departmentInfo['name'];
                $data[] = $subject;
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
            $user = User::model()->IsUserId($userId);
            if(!$user) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 用户token验证
            $userToken = UserToken::model()->IsToken($userId, $token);
//            var_dump($userToken);exit;
            if(!$userToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }

            $isExistUserMemberId = UserMember::model()->IsExistMemberId($userId, $memberId);
            if(!$isExistUserMemberId) {
                return 40003;        // MSG_ERR_FAIL_MEMBER
            }

            $isLessonArrangeId = self::IsLessonArrangeId($memberId, $lessonArrangeId);
            if(!$isLessonArrangeId) {
                return 60001;
            }

            $result = Yii::app()->cnhutong->createCommand()
                ->select('id as lessonStudentId, lesson_serial as lessonSerial,
                date, time, step, student_comment')
                ->from('ht_lesson_student')
                ->where('student_id = :studentId And lesson_arrange_id = :lessonArrangeId',
                    array(
                        ':studentId' => $memberId,
                        ':lessonArrangeId' => $lessonArrangeId
                    )
                )
                ->order('date asc')
                ->queryAll();

            // 判断数据是否为空数组
            if(ApiPublicController::array_is_null($result)) {
                $data[] = [];
            }

            foreach($result as $row) {
                // 获取数据
                $lessons = array();
                $lessons['lessonStudentId']                     = $row['lessonStudentId'];
                $lessons['lessonSerial']                        = $row['lessonSerial'];
                $lessons['lessonDate']                          = $row['date'] . ' ' . $row['time'];
                $lessons['lessonStatus']                        = self::getLessonStatus($row['step']);
                $lessons['lessonCharge']                        = $row['student_comment'];

                $data[] = $lessons;
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
            $user = User::model()->IsUserId($userId);
            if(!$user) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 用户token验证
            $userToken = UserToken::model()->IsToken($userId, $token);
//            var_dump($userToken);exit;
            if(!$userToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }

            $isExistUserMemberId = UserMember::model()->IsExistMemberId($userId, $memberId);
            if(!$isExistUserMemberId) {
                return 40003;        // MSG_ERR_FAIL_MEMBER
            }

            $isLessonStudentId = self::IsLessonStudentId($memberId, $lessonStudentId);
            if(!$isLessonStudentId) {
                return 60002;
            }

            $result = Yii::app()->cnhutong->createCommand()
                ->select('id as lessonStudentId, lesson_serial as lessonSerial, date, time,
                step, teacher_id as teacherId, student_rating, student_comment, lesson_content')
                ->from('ht_lesson_student')
                ->where('student_id = :studentId And id = :id',
                    array(
                        ':studentId' => $memberId,
                        ':id' => $lessonStudentId
                    )
                )
                ->order('date')
                ->queryAll();

            // 判断数据是否为空数组
            if(ApiPublicController::array_is_null($result)) {
                $data[] = [];
            }

            foreach($result as $row) {
                // 获取数据
                $lessonDetail = array();
                $lessonDetail['lessonStudentId']                = $row['lessonStudentId'];
                $lessonDetail['lessonSerial']                   = $row['lessonSerial'];
                $lessonDetail['lessonDate']                     = $row['date'] . ' ' . $row['time'];
                $lessonDetail['lessonStatus']                   = self::getLessonStatus($row['step']);
                $lessonDetail['teacherId']                      = $row['teacherId'];
                $lessonDetail['teacherName']                    = ApiPublicLesson::model()->getNameByMemberId($row['teacherId']);
                $lessonDetail['lessonScore']                    = $row['student_rating'];
                if(empty($lessonDetail['lessonScore'])) {
                    $lessonDetail['lessonScore']    = '';
                }
                $lessonDetail['lessonCharge']                   = $row['student_comment'];
                $lessonDetail['lessonContent']                  = $row['lesson_content'];
                if(empty($lessonDetail['lessonContent'])) {
                    $lessonDetail['lessonContent']    = '';
                }
                $data[] = $lessonDetail;
            }

//            $data = $result;
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }


    /**
     * 学员对上过的课时进行评价和打分
     * @param $userId                   -- 用户ID
     * @param $token                    -- 用户验证token
     * @param $memberId                 -- 用户当前绑定的学员对对应的ID
     * @param $lessonStudentId          -- 课时唯一编号
     * @param $score                    -- 学员给课时的评分，1-5分
     * @param $stateComment             -- 课时评价，可以为空
     * @return array|int
     */
    public function lessonStudent($userId, $token, $memberId, $lessonStudentId, $score, $stateComment)
    {
        $data = array();
        try {
            // 用户ID验证
            $user = User::model()->IsUserId($userId);
            if(!$user) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 用户token验证
            $userToken = UserToken::model()->IsToken($userId, $token);
//            var_dump($userToken);exit;
            if(!$userToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }

            $isExistUserMemberId = UserMember::model()->IsExistMemberId($userId, $memberId);
            if(!$isExistUserMemberId) {
                return 40003;        // MSG_ERR_FAIL_MEMBER
            }

            $isLessonStudentId = self::IsLessonStudentId($memberId, $lessonStudentId);
            if(!$isLessonStudentId) {
                return 60002;
            }

            if($score < 0 || $score > 5) {
                return 70001;
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

            if(empty($result)) {
                $data[] = [];
            }

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
            default:
                return '';
        }
    }

    /**
     * 输入：用户当前绑定的学员所对应的ID
     * 输出：该ID所对应的 课程唯一排课编号lessonArrangeId
     * @param $memberId
     * @param $lessonArrangeId
     * @return array
     */
    public function IsLessonArrangeId($memberId, $lessonArrangeId)
    {
        $arr = array();
        try {
            $arr = Yii::app()->cnhutong->createCommand()
                ->select('id')
                ->from('ht_lesson_student')
                ->where('student_id = :memberId And lesson_arrange_id = :lessonArrangeId',
                    array(
                        ':memberId' => $memberId,
                        ':lessonArrangeId' => $lessonArrangeId
                    )
                )
                ->queryAll();
        } catch (Exception $e) {
            error_log($e);
        }
        return $arr;
    }

    /**
     * 输入：用户当前绑定的学员所对应的ID
     * 输出：该ID所对应的 课时唯一编号 id
     * @param $memberId
     * @param $lessonStudentId
     * @return array
     */
    public function IsLessonStudentId($memberId, $lessonStudentId)
    {
        $id = '';
        try {
            $id = Yii::app()->cnhutong->createCommand()
                ->select('id')
                ->from('ht_lesson_student')
                ->where('student_id = :memberId And id = :lessonStudentId',
                    array(
                        ':memberId' => $memberId,
                        ':lessonStudentId' => $lessonStudentId
                    )
                )
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $id;
    }
}