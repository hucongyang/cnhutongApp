<?php
/*********************************************************
 * 错误码列表
 * 
 * @author  Lujia
 * @version 1.0 by Lujia @ 2013.12.23 创建错误列表
 ***********************************************************/
 
$_error_code = array(
		// 基本错误码
		'MSG_SUCCESS' 				=> array('10000', '成功'),
		'MSG_ERR_LESS_PARAM' 		=> array('10001', '请求缺少必要的参数'),
		'MSG_ERR_FAIL_PARAM' 		=> array('10002', '请求参数错误'),

        'MSG_ERR_INVALID_MOBILE'    => array('10003', '该手机号码已被注册'),
	    'MSG_SUCCESS_MOBILE'         => array('10004', '该手机号码可用'),

        'MSG_ERR_CODE_OVER_TIME'    => array('10005', '验证码已过期'),

        'MSG_ERR_UN_REGISTER_MOBILE'    => array('10006', '该手机号码未被注册,请先注册'),

        'MSG_ERR_FAIL_PASSWORD' 		=> array('10007', '密码错误'),

        'MSG_ERR_FAIL_DEPARTMENT'   => array('10008', '没有该校区信息'),

        'MSG_ERR_FAIL_TOKEN'     => array('10009', '传入的token错误'),

		// 用户相关错误码
        'MSG_NO_MEMBER' 			=> array('20001', '没有找到该姓名的用户'),
        'MSG_NO_LESSON' 			=> array('20002', '该学员当天没有课程'),
		'MSG_ERR_NO_TEACHER_LESSON'	=> array('20003', '学员没有这位老师的课程'),
		'MSG_ERR_NO_ADMIN'			=> array('20004', '管理员不存在或没有相关操作权限'),
		'MSG_ERR_NO_MORE_LESSONS'	=> array('20005', '该学员课程已结束，不能再进行加课'),

		// 其它
		'MSG_ERR_UNKOWN'			=> array('99999', '未知错误')
);

// return $ErrorCode;
