<?php

class MemberController extends ApiPublicController
{

    public function actionText()
    {
        var_dump(MobileCheckcode::model()->getCheckNum(6,0,9));
    }
    /**
     * 获取验证码                actionGetVerificationCode()
     * @param $mobile string    --手机号码
     * @param $type int        --类型：1表示注册新用户，2表示找回密码,3表示绑定手机
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     * 根据手机号获取注册验证码（用于注册新用户,找回密码,绑定手机)
     */
    public function actionGetVerificationCode()
    {
        //检查参数
        if(!isset($_REQUEST['mobile']) || !isset($_REQUEST['verifyType'])) {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $mobile = Yii::app()->request->getParam('mobile', NUll);
        $type = Yii::app()->request->getParam('verifyType', NULL);

        if(!$this->isMobile($mobile)) {
            $this->_return('MSG_ERR_FAIL_PARAM');
        }

        $aType = array('1', '2', '3');
        if(!in_array($type, $aType)) {
            $this->_return('MSG_ERR_FAIL_PARAM');
        }
        // 根据手机号码发送验证码
        $data = MobileCheckcode::model()->verificationCode($mobile, $type);
//        var_dump($data);exit;
        if($data === 10002) {
            $this->_return("MSG_ERR_FAIL_PARAM");
        } elseif ($data === 10003) {
            $this->_return("MSG_ERR_INVALID_MOBILE");
        } elseif ($data === 10006) {
            $this->_return("MSG_ERR_UN_REGISTER_MOBILE");
        } elseif ($data === 30001) {
            $this->_return("MSG_ERR_INVALID_BIND_MOBILE");
        }

        // 记录log

        $this->_return("MSG_SUCCESS");
    }

    /**
     * 注册       actionRegister()
     * @param $mobile int           --手机号码
     * @param $password string      --密码（md5加密）
     * @checkNum $checkNum int      --服务器发送的验证码
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionRegister()
    {
        // 检查参数
        if(!isset($_REQUEST['mobile']) || !isset($_REQUEST['password']) ||
            !isset($_REQUEST['checkNum'])) {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $mobile = Yii::app()->request->getParam('mobile', NULL);
        $password = Yii::app()->request->getParam('password', NULL);
        $checkNum = Yii::app()->request->getParam('checkNum', NULL);

        if(!$this->isMobile($mobile)) {
            $this->_return('MSG_ERR_FAIL_PARAM');
        }

        if(!$this->isPasswordValid($password)) {
            $this->_return('MSG_ERR_FAIL_PARAM');
        }
        //根据手机号，密码，验证码注册新用户
        $data = User::model()->register($mobile, $password, $checkNum);
        if($data === 10003) {
            $this->_return("MSG_ERR_INVALID_MOBILE");
        } elseif ($data === 10005) {
            $this->_return("MSG_ERR_CODE_OVER_TIME");
        } elseif ($data === 10006) {
            $this->_return("MSG_ERR_UN_REGISTER_MOBILE");
        }

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 用户绑定手机      actionBindMobile()
     * @param $mobile int           --手机号码
     * @param $password string      --密码（md5加密）
     * @checkNum $checkNum int      --服务器发送的验证码
     * @token $token  string         --登录token
     * @userId  $userId int         --用户id(APP中用户的唯一标识)
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionBindMobile()
    {

    }

    /**
     * 用户自动注册      actionAutoRegister()
     * @return result          调用返回结果
     * @return msg             调用返回结果说明
     * @return data             调用返回数据
     */
    public function actionAutoRegister()
    {
        // 自动注册
        $data = User::model()->autoRegister();

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 登录接口 actionLogin()  手机号码，密码登录
     *
     * @param $mobile string 注册时使用的手机号
     * @param $password string 注册时密码
     * @return $result          调用返回结果
     * @return $msg             调用返回结果说明
     * @return $data             调用返回数据
     */
    public function actionLogin()
    {
        if(!isset($_REQUEST['mobile']) || !isset($_REQUEST['password'])) {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $mobile = Yii::app()->request->getParam('mobile', NULL);
        $password = Yii::app()->request->getParam('password', NULL);

        if(!$this->isMobile($mobile)) {
            $this->_return('MSG_ERR_FAIL_MOBILE');
        }

//        if(!$this->isPasswordValid($password)) {
//            $this->_return('MSG_ERR_FAIL_PARAM');
//        }

        $data = User::model()->login($mobile, $password);
        if($data === 10006) {
            $this->_return('MSG_ERR_UN_REGISTER_MOBILE');
        } elseif ($data === 10007) {
            $this->_return('MSG_ERR_FAIL_PASSWORD');
        }

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 用户自动登录接口 actionUserVerify()  登录token(在系统中存放30天有效);用户id
     *
     * @param $token string 登录token
     * @param $userId int 用户id
     * @return $result          调用返回结果
     * @return $msg             调用返回结果说明
     * @return $data             调用返回数据
     */
    public function actionUserVerify()
    {
        if(!isset($_REQUEST['token']) || !isset($_REQUEST['userId'])) {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $token = Yii::app()->request->getParam('token', NULL);
        $userId = Yii::app()->request->getParam('userId', NULL);

        $data = User::model()->userVerify($userId, $token);
        if($data === 10002) {
            $this->_return('MSG_ERR_FAIL_PARAM');
        } elseif ($data === 10009) {
            $this->_return('MSG_ERR_FAIL_TOKEN');
        }

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 用户重置密码 actionResetPassword()
     *
     * @param $mobile string 注册用手机号
     * @param $password string 密码(未加密)
     * @param $checkNum string 服务器验证码
     * @return $result          调用返回结果
     * @return $msg             调用返回结果说明
     * @return $data             调用返回数据
     */
    public function actionResetPassword()
    {
        if(!isset($_REQUEST['mobile']) || !isset($_REQUEST['password']) || !isset($_REQUEST['checkNum']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }

        $mobile = Yii::app()->request->getParam('mobile', NULL);
        $password = Yii::app()->request->getParam('password', NULL);
        $checkNum = Yii::app()->request->getParam('checkNum', NULL);

        if(!$this->isMobile($mobile)) {
            $this->_return('MSG_ERR_FAIL_PARAM');
        }

        if(!$this->isPasswordValid($password)) {
            $this->_return('MSG_ERR_FAIL_PARAM');
        }
        //验证是否为合理的验证码格式  isCheckNum()

        $data = User::model()->resetPassword($mobile, $password, $checkNum);
        if($data === 10005) {
            $this->_return('MSG_ERR_CODE_OVER_TIME');
        } elseif ($data === 10006) {
            $this->_return('MSG_ERR_UN_REGISTER_MOBILE');
        }

        // 记录log

        $this->_return('MSG_SUCCESS', $data);
    }
}