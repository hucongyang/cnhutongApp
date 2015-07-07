<?php
class AdminController extends ApiPublicController
{
	/*******************************************************
	 * 学员指纹设置接口  actionSetFingerPrint
	 *
	 * @param $memberId		// 综合行政ID
     * @param $memberName   // 综合行政姓名
     * @param $fingerPrint  // 指纹信息
     * @param $type         // 1-实时提交  2-异步提交
     * @param $addTime      // 综合行政指纹录入时间
	 *
	 * @return $result		// 调用返回结果
	 * @return $msg			// 调用返回结果说明
	 *
	 * 说明：用户每个校区录入及修改学员的指纹（由综合行政进行操作）
	 *******************************************************/
    public function actionSetFingerPrint()
    {
        // 检查参数
        if (!isset($_REQUEST['memberId']) || !isset($_REQUEST['memberName']) || !isset($_REQUEST['fingerPrint'])
            || !isset($_REQUEST['type']) || !isset($_REQUEST['addTime'])) {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $memberId = Yii::app()->request->getParam('memberId', NULL);
        $memberName = Yii::app()->request->getParam('memberName', NULL);
        $fingerPrint = Yii::app()->request->getParam('fingerPrint', NULL);
        $type = Yii::app()->request->getParam('type', NULL);
        $addTime = Yii::app()->request->getParam('addTime', NULL);
        $departmentId = Yii::app()->request->getParam('department', NULL);
        $mac = Yii::app()->request->getParam('mac', NULL);

        // 检查综合行政是否存在
    	$name = HT_Member::model()->getNameByMemberId($memberId);
    	if(!$name)
    	{
    		$this->_return('MSG_NO_MEMBER');
    	}

        // 设置指纹
        $retCode = HT_Member::model()->setFingerPrint($memberId, $fingerPrint, 2, $departmentId, $addTime);
        if ($retCode == false) {
            $this->_return('MSG_ERR_UNKOWN');
        }

        // 记录LOG
        $memo = sprintf('type:%d', $type);
        HT_Log::model()->_admin_log($memberId, $departmentId, 0,
            						'SET_ADMIN_FINGER_PRINT', $addTime, $mac, $memo);

        // 发送返回值
        $this->_return('MSG_SUCCESS');
    }
    
    /*******************************************************
     * 删除管理员（综合行政）  actionRemoveAdmin
     *
     * @param $memberId		// 综合行政ID
     * @param $type         // 1-实时提交  2-异步提交
     * @param $sendTime     // 综合行政指纹录入时间
     *
     * @return $result		// 调用返回结果
     * @return $msg			// 调用返回结果说明
     *
     * 说明：删除管理员（综合行政）
     *******************************************************/
    public function actionRemoveAdmin()
    {
    	// 检查参数
    	if (!isset($_REQUEST['memberId']) || !isset($_REQUEST['type']) || !isset($_REQUEST['sendTime'])) {
    		$this->_return('MSG_ERR_LESS_PARAM');
    	}
    
    	$memberId = Yii::app()->request->getParam('memberId', NULL);
    	$type = Yii::app()->request->getParam('type', NULL);
    	$sendTime = Yii::app()->request->getParam('sendTime', NULL);
    	$departmentId = Yii::app()->request->getParam('department', NULL);
    	$mac = Yii::app()->request->getParam('mac', NULL);
    
    	// 检查综合行政是否存在
    	$name = HT_Member::model()->getNameByMemberId($memberId);
    	if(!$name)
    	{
    		$this->_return('MSG_NO_MEMBER');
    	}
    
    	// 删除综合行政（管理员）
    	$retCode = HT_Member::model()->removeAdmin($memberId, $departmentId);
		echo $retCode;
    	switch($retCode) {
    		case 0: 
    			break;
    		case 1: 
    			$this->_return('MSG_NO_MEMBER');
    			break;
    		case 2: 
    			$this->_return('MSG_ERR_UNKOWN');
    			break;
    		default:
    			$this->_return('MSG_ERR_UNKOWN');
    			break;
    	}
    	
    	// 记录LOG
    	$memo = sprintf('type:%d', $type);
    	HT_Log::model()->_admin_log($memberId, $departmentId, 0,
    								'REMOVE_ADMIN', $sendTime, $mac, $memo);
    
    	// 发送返回值
    	$this->_return('MSG_SUCCESS');
    }
}