<?php

class LessonController extends ApiPublicController
{
    /**
     * 获取学员所有正在进行课程的下一次上课时间 actionGetNextLessonList()
     *
     * 获取学员所有正在进行课程的下一次上课时间
     * @param $userId       -- 用户ID
     * @param $token        -- 用户验证token
     * @param $memberId     -- 用户当前绑定的学员所对应的ID
     * @return $result          调用返回结果
     * @return $msg             调用返回结果说明
     * @return $data             调用返回数据
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

        $data = HtLessonStudent::model()->nextLessonList($userId, $token, $memberId);
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
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
     * @return $result          调用返回结果
     * @return $msg             调用返回结果说明
     * @return $data             调用返回数据
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

        $data = HtLessonStudent::model()->allSubjects($userId, $token, $memberId);
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        }

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

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

        $data = HtLessonStudent::model()->SubjectSchedule($userId, $token, $memberId, $lessonArrangeId);
        if($data === 10010) {
            $this->_return('MSG_ERR_FAIL_USER');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        }
        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }
}