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

        'MSG_ERR_FAIL_USER'         => array('10010', '传入的用户ID错误'),

        'MSG_ERR_FAIL_MOBILE'       => array('10011', '手机号码错误'),

        'MSG_ERR_FAIL_REFEREE_MOBILE'       => array('10013', '推荐人手机号码错误'),

        'MSG_ERR_FAIL_SUBJECT'       => array('10012', '课程错误'),

		// 用户相关错误码
        'MSG_NO_MEMBER' 			=> array('20001', '没有找到该姓名的用户'),
        'MSG_NO_LESSON' 			=> array('20002', '该学员当天没有课程'),
		'MSG_ERR_NO_TEACHER_LESSON'	=> array('20003', '学员没有这位老师的课程'),
		'MSG_ERR_NO_ADMIN'			=> array('20004', '管理员不存在或没有相关操作权限'),
		'MSG_ERR_NO_MORE_LESSONS'	=> array('20005', '该学员课程已结束，不能再进行加课'),

        'MSG_ERR_OVER_THREE_MEMBERID'     => array('20006', '该用户已经绑定3个学员ID'),

        'MSG_ERR_INVALID_BIND_MOBILE'       => array('30001', '该手机号码已被绑定'),

        'MSG_ERR_SALT'                  => array('40001', '口令错误'),

        'MSG_ERR_INVALID_SALT'        => array('40002', '口令已被使用，失效'),

        'MSG_ERR_FAIL_MEMBER'         => array('40003', '传入的学员ID错误'),

        'MSG_ERR_NULL_SALT'                  => array('40004', '口令为NULL'),

        'MSG_MOBILE_PASSWORD_LOGIN'       => array('50000', '账号/密码登录'),

        'MSG_ERR_LESSON_ARRANGE_ID'       => array('60001', '课程的唯一排课编号错误'),

        'MSG_ERR_LESSON_STUDENT_ID'       => array('60002', '课时唯一编号错误'),

        'MSG_ERR_SCORE'                     => array('70001', '评分不在范围内(0-5分)'),

		// 其它
		'MSG_ERR_UNKOWN'			=> array('99999', '未知错误')
);

// return $ErrorCode;
