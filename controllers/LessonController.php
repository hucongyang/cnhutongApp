<?php

class LessonController extends ApiPublicController
{
    /**
     * 获取学员所有正在进行课程的下一次上课时间 actionGetNextLessonList()
     *
     * 获取学员所有正在进行课程的下一次上课时间
     * @userId $userId       -- 用户ID
     * @token $token        -- 用户验证token
     * @memberId $memberId     -- 用户当前绑定的学员所对应的ID
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionGetNextLessonList()
    {
        // 检查参数
        if(!isset($_REQUEST['userId']) || !isset($_REQUEST['token']) || !isset($_REQUEST['memberId']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $userId = Yii::app()->request->getParam('userId', NULL);
        $token = Yii::app()->request->getParam('token', NULL);
        $memberId = Yii::app()->request->getParam('memberId', NULL);

        $version            = Yii::app()->request->getParam('version', NULL);
        $deviceId           = Yii::app()->request->getParam('deviceId', NULL);
        $platform           = Yii::app()->request->getParam('platform', NULL);
        $channel            = Yii::app()->request->getParam('channel', NULL);
        $appVersion         = Yii::app()->request->getParam('appVersion', NULL);
        $osVersion          = Yii::app()->request->getParam('osVersion', NULL);
        $appId              = Yii::app()->request->getParam('appId', NULL);

        $data = HtLessonStudent::model()->nextLessonList($userId, $token, $memberId);
//        var_dump($data);exit;
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        } elseif ($data === 40003) {
            $this->_return('MSG_ERR_FAIL_MEMBER');
        }

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 获取学员所有的课程，包括正在学习的课程和历史课程  actionGetAllSubjects()
     *
     * @param $userId       -- 用户ID
     * @param $token        -- 用户验证token
     * @param $memberId     -- 用户当前绑定的学员所对应的ID
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionGetAllSubjects()
    {
        // 检查参数
        if(!isset($_REQUEST['userId']) || !isset($_REQUEST['token']) || !isset($_REQUEST['memberId']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $userId = Yii::app()->request->getParam('userId', NULL);
        $token = Yii::app()->request->getParam('token', NULL);
        $memberId = Yii::app()->request->getParam('memberId', NULL);

        $version            = Yii::app()->request->getParam('version', NULL);
        $deviceId           = Yii::app()->request->getParam('deviceId', NULL);
        $platform           = Yii::app()->request->getParam('platform', NULL);
        $channel            = Yii::app()->request->getParam('channel', NULL);
        $appVersion         = Yii::app()->request->getParam('appVersion', NULL);
        $osVersion          = Yii::app()->request->getParam('osVersion', NULL);
        $appId              = Yii::app()->request->getParam('appId', NULL);

        $data = HtLessonStudent::model()->allSubjects($userId, $token, $memberId);
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        } elseif ($data === 40003) {
            $this->_return('MSG_ERR_FAIL_MEMBER');
        }

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 获取学员指定课程的具体课时详情(课程表)
     * @param userId        -- 用户ID
     * @param token         -- 用户验证token
     * @param memberId      -- 用户当前绑定的学员所对应的ID
     * @param lessonArrangeId  -- 课程的唯一排课编号
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionGetSubjectSchedule()
    {
        // 检查参数
        if(!isset($_REQUEST['userId']) || !isset($_REQUEST['token'])
            || !isset($_REQUEST['memberId']) || !isset($_REQUEST['lessonArrangeId']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $userId = Yii::app()->request->getParam('userId', NULL);
        $token = Yii::app()->request->getParam('token', NULL);
        $memberId = Yii::app()->request->getParam('memberId', NULL);
        $lessonArrangeId = Yii::app()->request->getParam('lessonArrangeId', NULL);

        $version            = Yii::app()->request->getParam('version', NULL);
        $deviceId           = Yii::app()->request->getParam('deviceId', NULL);
        $platform           = Yii::app()->request->getParam('platform', NULL);
        $channel            = Yii::app()->request->getParam('channel', NULL);
        $appVersion         = Yii::app()->request->getParam('appVersion', NULL);
        $osVersion          = Yii::app()->request->getParam('osVersion', NULL);
        $appId              = Yii::app()->request->getParam('appId', NULL);

        $data = HtLessonStudent::model()->SubjectSchedule($userId, $token, $memberId, $lessonArrangeId);
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        } elseif ($data === 40003) {
            $this->_return('MSG_ERR_FAIL_MEMBER');
        } elseif ($data === 60001) {
            $this->_return('MSG_ERR_LESSON_ARRANGE_ID');
        }

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 获取学员指定课程的具体课时详情(课程表)
     * @param userId        -- 用户ID
     * @param token         -- 用户验证token
     * @param memberId      -- 用户当前绑定的学员所对应的ID
     * @param lessonStudentId  -- 课程的唯一排课编号
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionGetLessonDetails()
    {
        // 检查参数
        if(!isset($_REQUEST['userId']) || !isset($_REQUEST['token'])
            || !isset($_REQUEST['memberId']) || !isset($_REQUEST['lessonArrangeId']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $userId = Yii::app()->request->getParam('userId', NULL);
        $token = Yii::app()->request->getParam('token', NULL);
        $memberId = Yii::app()->request->getParam('memberId', NULL);
        $lessonStudentId = Yii::app()->request->getParam('lessonStudentId', NULL);

        $version            = Yii::app()->request->getParam('version', NULL);
        $deviceId           = Yii::app()->request->getParam('deviceId', NULL);
        $platform           = Yii::app()->request->getParam('platform', NULL);
        $channel            = Yii::app()->request->getParam('channel', NULL);
        $appVersion         = Yii::app()->request->getParam('appVersion', NULL);
        $osVersion          = Yii::app()->request->getParam('osVersion', NULL);
        $appId              = Yii::app()->request->getParam('appId', NULL);

        $data = HtLessonStudent::model()->lessonDetails($userId, $token, $memberId, $lessonStudentId);
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        } elseif ($data === 40003) {
            $this->_return('MSG_ERR_FAIL_MEMBER');
        } elseif ($data === 60002) {
            $this->_return('MSG_ERR_LESSON_STUDENT_ID');
        }
        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 学员对上过的课时进行评价和打分
     * @param userId        -- 用户ID
     * @param token         -- 用户验证token
     * @param memberId      -- 用户当前绑定的学员所对应的ID
     * @param lessonStudentId  -- 课程的唯一排课编号
     * @param score             -- 学员给课时的评分，0-5分
     * @param statement         -- 学员给课时的评价
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionEvalLessonStudent()
    {
        // 检查参数
        if(!isset($_REQUEST['userId']) || !isset($_REQUEST['token'])
            || !isset($_REQUEST['memberId']) || !isset($_REQUEST['lessonStudentId'])
            || !isset($_REQUEST['score']) || !isset($_REQUEST['statement']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $userId = Yii::app()->request->getParam('userId', NULL);
        $token = Yii::app()->request->getParam('token', NULL);
        $memberId = Yii::app()->request->getParam('memberId', NULL);
        $lessonStudentId = Yii::app()->request->getParam('lessonStudentId', NULL);
        $score = Yii::app()->request->getParam('score', NUll);
        $stateComment = Yii::app()->request->getParam('statement', NUll);

        $version            = Yii::app()->request->getParam('version', NULL);
        $deviceId           = Yii::app()->request->getParam('deviceId', NULL);
        $platform           = Yii::app()->request->getParam('platform', NULL);
        $channel            = Yii::app()->request->getParam('channel', NULL);
        $appVersion         = Yii::app()->request->getParam('appVersion', NULL);
        $osVersion          = Yii::app()->request->getParam('osVersion', NULL);
        $appId              = Yii::app()->request->getParam('appId', NULL);

        $data = HtLessonStudent::model()->lessonStudent($userId, $token, $memberId, $lessonStudentId, $score, $stateComment);
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        } elseif ($data === 40003) {
            $this->_return('MSG_ERR_FAIL_MEMBER');
        } elseif ($data === 60002) {
            $this->_return('MSG_ERR_LESSON_STUDENT_ID');
        } elseif ($data === 70001) {
            $this->_return('MSG_ERR_SCORE');
        }
        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 学员在APP中对自己的课时进行请假或者取消请假的操作
     * @param userId        -- 用户ID
     * @param token         -- 用户验证token
     * @param memberId      -- 用户当前绑定的学员所对应的ID
     * @param lessonStudentId  -- 课程的唯一排课编号
     * @param leaveType            -- 请假类型 1表示请假，2表示取消请假
     * @param issue                -- 预约补课时间
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionLessonStudentLeave()
    {
        // 检查参数
        if(!isset($_REQUEST['userId']) || !isset($_REQUEST['token'])
            || !isset($_REQUEST['memberId']) || !isset($_REQUEST['lessonStudentId'])
            || !isset($_REQUEST['leaveType']) || !isset($_REQUEST['issue']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $userId = Yii::app()->request->getParam('userId', NULL);
        $token = Yii::app()->request->getParam('token', NULL);
        $memberId = Yii::app()->request->getParam('memberId', NULL);
        $lessonStudentId = Yii::app()->request->getParam('lessonStudentId', NULL);
        $leaveType = Yii::app()->request->getParam('leaveType', NUll);
        $issue = Yii::app()->request->getParam('issue', NUll);

        $version            = Yii::app()->request->getParam('version', NULL);
        $deviceId           = Yii::app()->request->getParam('deviceId', NULL);
        $platform           = Yii::app()->request->getParam('platform', NULL);
        $channel            = Yii::app()->request->getParam('channel', NULL);
        $appVersion         = Yii::app()->request->getParam('appVersion', NULL);
        $osVersion          = Yii::app()->request->getParam('osVersion', NULL);
        $appId              = Yii::app()->request->getParam('appId', NULL);

        $data = HtLessonStudent::model()->lessonStudentLeave($userId, $token, $memberId, $lessonStudentId, $leaveType, $issue);
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        }
        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }
}