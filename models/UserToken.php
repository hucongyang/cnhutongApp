<?php

class UserToken extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{user_token}}';
    }

    /**
     *  生成32位不重复的随机数
     * @param $param int 随机字符串位数
     * @return string 产生的token
     */
    public function getRandomToken($param) {
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $token = "";
        for($i = 0; $i < $param; $i++) {
            $token .= $str{mt_rand(0, 61)};       //生成php随机数
        }
        return $token;
    }

    /**
     * 根据userId获得自动登录token,如token过期则重新生成
     * @param $userId
     * @return array|bool
     */
    public function getToken($userId)
    {
        $data['token'] = array();
        try {
            $user_token = Yii::app()->cnhutong_user->createCommand()
                ->select('id, expire_ts')
                ->from('user_token')
                ->where('user_id = :userId', array(':userId' => $userId))
                ->queryRow();
            if(!$user_token) {
                return false;
            }
//            $expire_ts = "0000-00-00 00:00:00";
            $expire_ts = $user_token['expire_ts'];

            if(strtotime($expire_ts) > strtotime("now")) {
                $createTime = date("Y-m-d H:i:s");
                $expireTime = date("Y-m-d H:i:s", strtotime("+30 days"));     //token 过期时间，暂时为30天，具体待定
                //重新生成token update
                $token = self::getRandomToken(32);
                Yii::app()->cnhutong_user->createCommand()
                    ->update('user_token',
                        array(
                            'token' => $token,
                            'create_ts' => $createTime,
                            'expire_ts' => $expireTime
                        ),
                        'user_id = :userId',
                        array(':userId' => $userId)
                    );
            }

            // 取得token
            $token_model = Yii::app()->cnhutong_user->createCommand()
                ->select('token')
                ->from('user_token')
                ->where('user_id = :userId', array(':userId' => $userId))
                ->queryRow();
            if(!$token_model) {
                return false;
            }
            $data['token'] = $token_model['token'];
        } catch (Exception $e) {
            error_log($e);
        }
        return $data['token'];
    }

    /**
     * 前端post 过来的token，userId判断此用户的token是否过期
     * @param $userId
     * @param $token
     * @return bool
     */
    public function IsToken($userId, $token)
    {
        $userToken = '';
        $nowTime = strtotime("now");  // 当前时间戳
        try {
            $userToken = Yii::app()->cnhutong_user->createCommand()
                ->select('id, create_ts, expire_ts')
                ->from('user_token')
                ->where('user_id = :userId And token = :token',
                    array(
                        ':userId' => $userId,
                        ':token' => $token
                    )
                )
                ->queryRow();
            if(!$userToken) {
                return false;
            }
            $token_create = strtotime($userToken['create_ts']);
            $token_expire = strtotime($userToken['expire_ts']);
            if($nowTime < $token_create || $nowTime > $token_expire) {
                return false;
            }
        } catch (Exception $e) {
            error_log($e);
        }
        return $userToken;
    }
}