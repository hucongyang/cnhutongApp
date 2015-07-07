<?php

/*********************************************************
 * This is the model class for log
 * 
 * @package HT_Log
 * @author  Lujia
 *
 * @version 1.0 by Lujia @ 2015.05.08 创建类以及相关的操作
 ***********************************************************/

class HT_Log extends CActiveRecord
{
	/*******************************************************
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     *******************************************************/
	public static function model($className = __CLASS__) 
	{
        return parent::model($className);
    }
    
    private $log_admin_code = array(
    	// 管理员操作log 对应编号
    	'SET_MEMBER_FINGER_PRINT'	=> 1,		// 设置学员指纹
    	'ADD_LESSON'				=> 2,		// 学员加课打印
    	'SET_ADMIN_FINGER_PRINT'	=> 3,		// 设置综合行政指纹
    	'GET_ADMIN_INFO'			=> 4,		// 获取综合行政列表
    	'REMOVE_ADMIN'			    => 5,		// 删除综合行政人员
    	'GET_MEMBER_INFO'		    => 6,		// 获取学员信息
    	'GET_TEACHER_INFO'			=> 7,		// 获取教师信息
        'GET_CLASS_INFO'			=> 8,		// 获取课程列表
	);
	
	/*******************************************************
	 * 管理员操作LOG记录       _admin_log
	 *
     * @param $memberId		// 用户ID
     * @param $adminId		// 管理员ID
     * @param $departmentId // 校区ID
     * @param $logType		// 用户操作类型
     * @param $mac  		// 机器mac地址
	 * @param $sendTime		// 用户操作时间（客户端时间）
	 *
	 * 说明：管理员操作对应的log记录
	 *******************************************************/
	public function _admin_log($memberId, $departmentId, $adminId, $logType, $sendTime, $mac, $memo = NULL)
	{
        // $table_name = sprintf('user_log_%s', date('Ym'));
        $time = date("Y-m-d H:i:s");
        $table_name = 'ht_log_admin_fingerprint';
		$con_hutong = Yii::app()->db_hutong;
		try	{
            $con_hutong->createCommand()->insert($table_name,
					array('member_id'		=> $memberId,
                          'department_id'	=> $departmentId,
                          'admin_id'		=> $adminId,
                          'action'			=> $this->log_admin_code[$logType],
                          'act_ts'			=> $time,
                          'send_ts'		    => $sendTime,
                          'mac'             => $mac,
						  'memo'			=> $memo));
		} catch(Exception $e) {
			error_log($e);
		}
	}
	
	/*******************************************************
	 * 签到LOG记录      		_sign_log
	 *
	 * @param $memberId		// 用户ID
	 * @param $teacherId	// 教师ID
	 * @param $adminId		// 管理员ID
	 * @param $departmentId // 校区ID
	 * @param $date			// 日期
	 * @param $time			// 时间
	 * @param $lessonId		// 课程ID
	 * @param $courseId		// 科目ID
	 * @param $status		// 状态  1-正常  2-重复 3-已入库（正常）
	 * @param $mac  		// 机器mac地址
	 * @param $sendTime		// 用户操作时间（客户端时间）
	 * @param $memo			// 备注
	 *
	 * 说明：签到LOG记录
	 *******************************************************/
	public function _sign_log($memberId, $teacherId, $departmentId, $adminId, $date, $time, 
							  $lessonId, $courseId, $signType, $status, $sendTime, $mac, $memo = NULL)
	{
		// $table_name = sprintf('user_log_%s', date('Ym'));
		$createTs = date("Y-m-d H:i:s");
		$table_name = 'ht_log_member_sign';
		$con_hutong = Yii::app()->db_hutong;
		try	{
			$con_hutong->createCommand()->insert($table_name,
					array('member_id'		=> $memberId,
							'teacher_id'	=> $teacherId,
							'department_id'	=> $departmentId,
							'date'			=> $date,
							'time'			=> $time,
							'lesson_id'		=> $lessonId,
							'course_id'		=> $courseId,
							'admin_id'		=> $adminId,
							'sign_type'		=> $signType,
							'status'		=> $status,
							'sign_ts'		=> $sendTime,
							'create_ts'		=> $createTs,
							'mac'           => $mac,
							'memo'			=> $memo));
		} catch(Exception $e) {
			error_log($e);
		}
	}
	 
}