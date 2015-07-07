<?php

class LessonController extends ApiPublicController
{
    /**
     * 获取学员所有正在进行课程的下一次上课时间 actionGetNextLessonList()
     *
     * @param $token string 登录token,用户验证token
     * @param $userId int 用户id
     * @param $memberId int 用户当前绑定的学员所对应的ID  ps: cnhutong 的 ht_member 表对应的 id (ht_lesson_student student_id); cnhutong_user user_member表 member_id
     * @return $result          调用返回结果
     * @return $msg             调用返回结果说明
     * @return $data             调用返回数据
     */
    public function actionGetNextLessonList()
    {
        if(!isset($_REQUEST['userId']) || !isset($_REQUEST['token']) || !isset($_REQUEST['memberId']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $userId = Yii::app()->request->getParam('userId', NULL);
        $token = Yii::app()->request->getParam('token', NULL);
        $memberId = Yii::app()->request->getParam('memberId', NULL);

        $transaction = Yii::app()->cnhutong->beginTransaction();
        $data = array();
        try {
            $isUserId = User::IsUserId($userId);
            if(!$isUserId) {
                $this->_return('MSG_ERR_FAIL_PARAM');
            }
            $isToken = UserToken::IsToken($userId, $token);
            if(!$isToken) {
                $this->_return('MSG_ERR_FAIL_TOKEN');
            }

            $where = 'ls.student_id = ' . $memberId;
            $nextLessonList = Yii::app()->cnhutong->createCommand()
                ->select('')
                ->from('ht_lesson_student ls')
                ->join('')
                ->where($where)
                ->queryAll();
            var_dump($nextLessonList);exit;
        } catch (Exception $e) {
            error_log($e);
            $transaction->rollback();
            $this->_return('MSG_ERR_UNKOWN');
        }
        $this->_return('MSG_SUCCESS', $data);
    }
}