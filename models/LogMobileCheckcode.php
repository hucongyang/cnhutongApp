<?php

class LogMobileCheckcode extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{log_mobile_checkcode}}';
    }

    /**
     * 手机验证码:根据参数type类型对应操作       verificationCode
     * @param $mobile               --手机号码
     * @param $type                 1-注册    2-找回密码      3-绑定手机
     * @return int                  返回对应的信息
     */
    public function verificationCode($mobile, $type)
    {
        $nowTime = date("Y-m-d H:i:s");
        $overTime = date("Y-m-d H:i:s", strtotime("+5 minutes"));
        //获得验证码，随机产生6位数字
        $checkNum = self::getCheckNum(6, 0, 9);
        if($type == 1) {        //注册验证码
            try {
                if(User::model()->getUserByMobile($mobile)) {
                    return 10003;       //MSG_ERR_INVALID_MOBILE
                }
                //该手机号码第一次发送验证码，insert
                if(!self::getIsExistMobile($mobile)) {
                    $mobile_checkcode_insert = Yii::app()->cnhutong_user->createCommand()
                        ->insert('log_mobile_checkcode',
                            array(
                                'mobile' => $mobile,
                                'checknum' => $checkNum,
                                'create_ts' => $nowTime,
                                'expire_ts' => $overTime,
                                'status' => 0
                            )
                        );
                } else {
                    //手机号码已发送验证码，再次发送, update
                    $mobile_checkcode_update = Yii::app()->cnhutong_user->createCommand()
                        ->update('log_mobile_checkcode',
                            array(
                                'checknum' => $checkNum,
                                'create_ts' => $nowTime,
                                'expire_ts' => $overTime,
                                'status' => 0
                            ),
                            'mobile = :mobile',
                            array(':mobile' => $mobile)
                        );
                }
            } catch (Exception $e) {
                error_log($e);
            }
        } elseif ($type == 2) {     //找回密码验证码
            try {
                if(!User::model()->getUserByMobile($mobile)) {
                    return 10006;        //MSG_ERR_UN_REGISTER_MOBILE
                }
                // 该手机号码不在mobile_checkcode表里面(ps:删除了) insert
                if(!self::getIsExistMobile($mobile)) {
                    $mobile_checkcode_insert = Yii::app()->cnhutong_user->createCommand()
                        ->insert('log_mobile_checkcode',
                            array(
                                'mobile' => $mobile,
                                'checknum' => $checkNum,
                                'create_ts' => $nowTime,
                                'expire_ts' => $overTime,
                                'status' => 0
                            )
                        );
                } else {
                    // 手机号码已存在mobile_checknum表里面 update
                    $mobile_checkcode = Yii::app()->cnhutong_user->createCommand()
                        ->update('log_mobile_checkcode',
                            array(
                                'checknum' => $checkNum,
                                'create_ts' => $nowTime,
                                'expire_ts' => $overTime,
                                'status' => 0
                            ),
                            'mobile = :mobile',
                            array(':mobile' => $mobile)
                        );
                }
            } catch (Exception $e) {
                error_log($e);
            }
        } elseif ($type == 3) {        //  绑定手机发送验证码
            try {
                if(User::model()->getUserByMobile($mobile)) {
                    return 30001;       //  MSG_ERR_INVALID_BIND_MOBILE
                }
                //该手机号码第一次发送验证码，insert
                if(!self::getIsExistMobile($mobile)) {
                    $mobile_checkcode_insert = Yii::app()->cnhutong_user->createCommand()
                        ->insert('log_mobile_checkcode',
                            array(
                                'mobile' => $mobile,
                                'checknum' => $checkNum,
                                'create_ts' => $nowTime,
                                'expire_ts' => $overTime,
                                'status' => 0
                            )
                        );
                } else {
                    //手机号码已发送验证码，再次发送, update
                    $mobile_checkcode_update = Yii::app()->cnhutong_user->createCommand()
                        ->update('log_mobile_checkcode',
                            array(
                                'checknum' => $checkNum,
                                'create_ts' => $nowTime,
                                'expire_ts' => $overTime,
                                'status' => 0
                            ),
                            'mobile = :mobile',
                            array(':mobile' => $mobile)
                        );
                }
            } catch (Exception $e) {
                error_log($e);
            }
        } else {
            return 10002;       //MSG_ERR_FAIL_PARAM
        }

        // 短信接口

//        return true;
        return $checkNum;       // 返回的验证码目前测试用，正式上线去除
    }

    /**
     * 随机生成6位数字验证码
     * @return array
     */
    public function getNum()
    {
        $range = range(100000, 999999);
        return array_rand($range, 1);
    }

    /**
     * 生成验证码
     * @param $num int 位数
     * @param $start int 开始值
     * @param $end int 结束值
     * @return string 生成的数字验证码
     */
    public function getCheckNum($num, $start, $end)
    {
        $range= range($start, $end);
        $arr =array();
        for($i = 0; $i < $num; $i++) {
            $arr[] = array_rand($range);
        }
        return implode("", $arr);
    }

    /**
     * 查找mobile_checkcode表里面是否存在$mobile
     * @param $mobile string 手机号码
     * @return bool
     */
    public function getIsExistMobile($mobile)
    {
        $mobile_id = Yii::app()->cnhutong_user->createCommand()
            ->select('id')
            ->from('log_mobile_checkcode')
            ->where('mobile = :mobile', array(':mobile' => $mobile))
            ->queryScalar();
        if(!$mobile_id) {
            return false;
        }
        return true;
    }

    /**
     * 根据$mobile, $checknum 判断验证码是否过期
     * @param $mobile
     * @param $checknum
     * @return bool
     */
    public function checkCode($mobile, $checknum)
    {
        $nowTime = strtotime("now");    //当前时间戳
        $check = Yii::app()->cnhutong_user->createCommand()
            ->select('id, create_ts, expire_ts')
            ->from('log_mobile_checkcode')
            ->where('mobile = :mobile And checknum = :checknum',
                array(
                    ':mobile' => $mobile,
                    ':checknum' => $checknum
                )
            )
            ->queryRow();
        if(!$check) {
            return false;
        }
        $check_create = strtotime($check['create_ts']);
        $check_expire = strtotime($check['expire_ts']);
        if($nowTime < $check_create || $nowTime > $check_expire) {
            return false;
        }
        return true;
    }
}