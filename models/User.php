<?php

class User extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{user}}';
    }

    /**
     * 用户使用手机号进行注册新用户
     * @param $mobile           --手机号
     * @param $password         --密码
     * @param $checkNum         --验证码
     * @return int
     */
    public function register($mobile, $password, $checkNum)
    {
//        $passwordMd5 = md5($password);
        $data = array();
        try {
            if(User::getUserByMobile($mobile)) {
                return 10003;       // MSG_ERR_INVALID_MOBILE
            }
            // 验证码是否过期
            if(!MobileCheckcode::model()->checkCode($mobile, $checkNum)) {
                return 10005;       //  MSG_ERR_CODE_OVER_TIME   验证码过期
            }
            // 注册成功插入数据
            $registerTime = date("Y-m-d H-i-s", strtotime("now"));        //用户注册时间
            $registerIp = self::getClientIP();                              //用户注册IP
            $lastLoginTime = date("Y-m-d H-i-s", strtotime("now"));       //用户最后登录时间，当前为注册时间
            $lastLoginIp = self::getClientIP();                             //用户最后登录IP，当前为注册IP
            Yii::app()->cnhutong_user->createCommand()
                ->insert('user',
                    array(
                        'mobile' => $mobile,
                        'password' => $password,
                        'register_time' => $registerTime,
                        'register_ip' => $registerIp,
                        'last_login_time' => $lastLoginTime,
                        'last_login_ip' =>$lastLoginIp,
                    ));
            //注册成功,验证码使用后改变验证码status状态
            Yii::app()->cnhutong_user->createCommand()
                ->update('mobile_checkcode',
                    array(
                        'status' => 1
                    ),
                    'mobile = :mobile',
                    array(':mobile' => $mobile)
                );

            //获得userId
            $userId = self::getUserByMobile($mobile);
            if(!$userId) {
                return 10006;       //  MSG_ERR_UN_REGISTER_MOBILE
            }
            //注册成功生成token
            $token = UserToken::getRandomToken(32);
            $type = '';
            $create_ts = date("Y-m-d H-i-s", strtotime("now"));
            $expire_ts_token = date("Y-m-d H-i-s", strtotime("+30 day"));
            Yii::app()->cnhutong_user->createCommand()
                ->insert('user_token',
                    array(
                        'user_id' => $userId,
                        'token' => $token,
                        'type' => $type,
                        'create_ts' => $create_ts,
                        'expire_ts' => $expire_ts_token
                    )
                );

            //userId
            $data['userId'] = $userId;
            //members
            $data['members'] = UserMember::getMembers($userId);
            if(!$data['members']) {
                $data['members'] = "尚未绑定学员id";
            }
            //token
            $data['token'] = UserToken::getToken($userId);
            //用户昵称，积分，等级 等待后续开发
            $data['nickname'] = 'nickname';
            $data['points'] = 'points';
            $data['level'] = 'level';

        } catch(Exception $e) {
            error_log($e);
        }

        return $data;
    }


    /**
     * 用户使用手机号/密码登录
     * @param $mobile
     * @param $password
     * @return array|int
     */
    public function login($mobile, $password)
    {
//        $passwordMd5 = md5($password);
        $data = array();
        try {
            $user = self::getUserByMobile($mobile);
            if(!$user) {
                return 10006;           // MSG_ERR_UN_REGISTER_MOBILE
            }
            $userId = self::getUserByMobilePassword($mobile, $password);
            if(!$userId) {
                return 10007;           //  MSG_ERR_FAIL_PASSWORD
            }
            //userId
            $data['userId'] = $userId;
            //members
            $data['members'] = UserMember::getMembers($userId);
            if(!$data['members']) {
                $data['members'] = "尚未绑定学员id";
            }
            //token
            $data['token'] = UserToken::getToken($userId);
            //用户昵称，积分，等级 等待后续开发
            $data['nickname'] = 'nickname';
            $data['points'] = 'points';
            $data['level'] = 'level';

        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }

    /**
     * 用户自动登录，token自动登录
     * @param $userId
     * @param $token
     * @return array|int
     */
    public function userVerify($userId, $token)
    {
        $data = array();
        try {
            $user = self::IsUserId($userId);
            if(!$user) {
                return 10002;       //  MSG_ERR_FAIL_PARAM
            }
            $userToken = UserToken::IsToken($userId, $token);
//            var_dump($userToken);exit;
            if(!$userToken) {
                return 10009;       //  MSG_ERR_FAIL_TOKEN
            }
            //userId
            $data['userId'] = $userId;
            //members
            $data['members'] = UserMember::getMembers($userId);
            if(!$data['members']) {
                $data['members'] = "尚未绑定学员id";
            }
            //token
            $data['token'] = UserToken::getToken($userId);
            //用户昵称，积分，等级 等待后续开发
            $data['nickname'] = 'nickname';
            $data['points'] = 'points';
            $data['level'] = 'level';

        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }

    /**
     * 用户忘记密码后使用手机号获得验证码重置密码
     * @param $mobile
     * @param $password
     * @param $checkNum
     * @return array|int
     */
    public function resetPassword($mobile, $password, $checkNum)
    {
//        $passwordMd5 = md5($password);
        $data = array();
        try {
            $userId = self::getUserByMobile($mobile);
            if(!$userId) {
                return 10006;           //  MSG_ERR_UN_REGISTER_MOBILE
            }
            $mobile_checkcode = MobileCheckcode::model()->checkCode($mobile, $checkNum);
            if(!$mobile_checkcode) {
                return 10005;           //  MSG_ERR_CODE_OVER_TIME
            }
            //手机号码已注册且验证码正确  update
            Yii::app()->cnhutong_user->createCommand()
                ->update('user',
                    array(
                        'password' => $password,
                    ),
                    'mobile = :mobile',
                    array(':mobile' => $mobile)
                );
            //修改成功,验证码使用后改变验证码status状态
            Yii::app()->cnhutong_user->createCommand()
                ->update('mobile_checkcode',
                    array(
                        'status' => 1
                    ),
                    'mobile = :mobile',
                    array(':mobile' => $mobile)
                );

            //userId
            $data['userId'] = $userId;
            //members
            $data['members'] = UserMember::getMembers($userId);
            if(!$data['members']) {
                $data['members'] = "尚未绑定学员id";
            }
            //token
            $data['token'] = UserToken::getToken($userId);
            //用户昵称，积分，等级 等待后续开发
            $data['nickname'] = 'nickname';
            $data['points'] = 'points';
            $data['level'] = 'level';

        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }

    /**
     * 根据手机号获得user id
     * @param $mobile
     * @return mixed
     */
    public function getUserByMobile($mobile)
    {
        $user = Yii::app()->cnhutong_user->createCommand()
            ->select('id')
            ->from('user')
            ->where('mobile = :mobile', array(':mobile' => $mobile))
            ->queryScalar();
        return $user;
    }

    /**
     * 根据手机号，注册密码获得 user id
     * @param $mobile
     * @param $password
     * @return mixed
     */
    public function getUserByMobilePassword($mobile, $password)
    {
        $user = Yii::app()->cnhutong_user->createCommand()
            ->select('id')
            ->from('user')
            ->where('mobile = :mobile And password = :password And status = 1', array(
                ':mobile' => $mobile,
                ':password' => $password
            ))
            ->queryScalar();
        return $user;
    }

    /**
     * 根据post参数userId判断是否为有效的userId
     * @param $userId
     * @return mixed
     */
    public function IsUserId($userId)
    {
        $user = Yii::app()->cnhutong_user->createCommand()
            ->select('id')
            ->from('user')
            ->where('id = :id And status = 1', array(':id' => $userId))
            ->queryScalar();
        return $user;
    }

    /*******************************************************
     * 获取连接IP
     * @author Lujia
     * @create 2013/12/26
     *******************************************************/
    public function getClientIP()
    {
        if (getenv("HTTP_CLIENT_IP"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        else if(getenv("HTTP_X_FORWARDED_FOR"))
        {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        else if(getenv("REMOTE_ADDR"))
        {
            $ip = getenv("REMOTE_ADDR");
        }
        else
        {
            $ip = "Unknow";
        }
        return $ip;
    }
}