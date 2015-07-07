<?php
class DataController extends ApiPublicController
{
	// TODO : 把对应的models进行拆分到HT_Data中去
	
	/*******************************************************
	 * 获取综合行政管理员列表接口  actionGetAdmins
	 *
	 * 无输入参数
	 *
	 * @return $result		        // 调用返回结果
	 * @return $msg			        // 调用返回结果说明
     * @return data
     * {
     *    @return $admins           // 管理员数组
     *    {
     *      @return $memberId       // 综合行政ID
     *      @return $name           // 综合行政姓名
     *      @return $fingerPrint    // 对应指纹信息
     *    }
     * }
	 *
	 * 说明：用户每个校区录入及修改学员的指纹（由综合行政进行操作）
	 *******************************************************/
    public function actionGetAdmins()
    {
        $departmentId = Yii::app()->request->getParam('department', NULL);
        $mac = Yii::app()->request->getParam('mac', NULL);

        // 获取管理员信息
        $data = HT_Data::model()->getAdmins($departmentId);

        // 记录LOG
        HT_Log::model()->_admin_log(0, $departmentId, 0, 'GET_ADMIN_INFO', NULL, $mac);

        // 发送返回值
        $this->_return('MSG_SUCCESS', $data);
    }
	
    /*******************************************************
     * 获取学员列表	        	actionGetStudents
     *
     * @return $result			// 调用返回结果
     * @return $msg				// 调用返回结果说明
     * @return $data
     * {
     *   @return $students  	// 学员数组
     *   {
     *     @return $memberId	// 学员ID
     *     @return $name      	// 学员姓名
     *     @return $fingerPrint // 学员指纹信息
     *   }
     * }
     *
     * 说明：获取学员列表
     *******************************************************/
    public function actionGetStudents()
    {
    	// 检查参数 : no param
    	 
    	$departmentId = Yii::app()->request->getParam('department', NULL);
    	$mac = Yii::app()->request->getParam('mac', NULL);
    
    	// 获取学员列表
    	$data = HT_Data::model()->getStudents($departmentId);
    
    	// 记录LOG
    	HT_Log::model()->_admin_log(0, $departmentId, 0, 'GET_MEMBER_INFO', NULL, $mac);
    	 
    	// 发送返回值
    	$this->_return('MSG_SUCCESS', $data);
    }
    
    /*******************************************************
     * 获取课程列表	        	actionGetLessons
     *
     * @return $result			// 调用返回结果
     * @return $msg				// 调用返回结果说明
     * @return $data
     * {
     *   @return $lessons	  	// 课程数组
     * }
     *
     * 说明：获取课程列表
     *******************************************************/
    public function actionGetLessons()
    {
    	// 检查参数 : no param
    
    	$departmentId = Yii::app()->request->getParam('department', NULL);
    	$mac = Yii::app()->request->getParam('mac', NULL);
    
    	// 获取课程列表
    	$data = HT_Data::model()->getLessons($departmentId);
    
    	// 记录LOG
    	HT_Log::model()->_admin_log(0, $departmentId, 0, 'GET_CLASS_INFO', NULL, $mac);
    	
    	// 发送返回值
    	$this->_return('MSG_SUCCESS', $data);
    }
	
    /*******************************************************
     * 获取老师列表	        	actionGetTeachers
     *
     * @return $result			// 调用返回结果
     * @return $msg				// 调用返回结果说明
     * @return $data
     * {
     *   @return $teachers  	// 老师数组
     *   {
     *     @return $memberId	// 老师ID
     *     @return $name      	// 老师姓名
     *     @return $title		// 老师职位
     *   }
     * }
     *
     * 说明：获取老师列表
     *******************************************************/
    public function actionGetTeachers()
    {
    	// 检查参数 : no param
    
    	$departmentId = Yii::app()->request->getParam('department', NULL);
    	$mac = Yii::app()->request->getParam('mac', NULL);
    
    	// 获取老师列表
    	$data = HT_Data::model()->getTeachers($departmentId);
    
    	// 记录LOG
    	HT_Log::model()->_admin_log(0, $departmentId, 0, 'GET_TEACHER_INFO', NULL, $mac);
    	 
    	// 发送返回值
    	$this->_return('MSG_SUCCESS', $data);
    }
}