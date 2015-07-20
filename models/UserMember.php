<?php

class UserMember extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{user_member}}';
    }

    /**
     * 根据app唯一userId获得学员members
     * @param $userId
     * @return array|bool
     */
    public function getMembers($userId)
    {
        $aMembers = array();
        $data['members'] = array();
        try {
            $member_model = Yii::app()->cnhutong_user->createCommand()
                ->select('id, member_id, status')
                ->from('user_member')
                ->where('user_id = :userId', array(':userId' => $userId))
                ->queryAll();
            if(!$member_model) {
                return false;
            }
            foreach($member_model as $row) {
                $aMembers[] = array(
                    'name' => '秦汉胡同',
                    'memberId' => $row['member_id'],
                    'memberStatus' => $row['status']
                );
//                array_push($data['members'], $aMembers);
                $data['members'] = $aMembers;
            }
        } catch (Exception $e) {
            error_log($e);
        }
        return $data['members'];
    }

    /**
     * 用户绑定学员信息
     * @param $userId
     * @param $token
     * @param $salt
     * @return array
     */
    public function bindMember($userId, $token, $salt)
    {
        $data = array();
        try {
            // 验证userId
            $user = User::model()->IsUserId($userId);
            if(!$user) {
                return 10002;       // MSG_ERR_FAIL_PARAM
            }
            // 验证token
            $userToken = UserToken::IsToken($userId, $token);
            if($userToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }
            // 获取memberId
            $memberId = self::getMemberIdBySalt($salt);
            if(!$memberId) {
                return 40001;       // MSG_ERR_SALT
            }
            // salt 口令是否已使用
            $userMemberId = self::IsExistMemberId($userId, $memberId);
            if($userMemberId) {
                return 40002;       // MSG_ERR_INVALID_SALT
            }
            // 验证通过后，insert数据
            $nowTime = date("Y-m-d", strtotime("now"));
            $user_member = Yii::app()->cnhutong_user->createCommand()
                ->insert('user_member',
                    array(
                        'user_id' => $userId,
                        'member_id' => $memberId,
                        'status' => 1,
                        'create_ts' => $nowTime
                    )
                );
        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }

    /**
     * 输入：口令$salt
     * 输出: 学员id  $memberId
     * @param $salt
     * @return string
     */
    public function getMemberIdBySalt($salt)
    {
        $memberId = '';
        try {
            $memberId = Yii::app()->cnhutong->createCommand()
                ->select('id')
                ->from('ht_member')
                ->where('salt = :salt', array(':salt' => $salt))
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $memberId;
    }

    /**
     * 输入：用户App唯一 userId ; 学员 memberId
     * 输出: $id
     * @param $userId
     * @param $memberId
     * @return string
     */
    public function IsExistMemberId($userId, $memberId)
    {
        $id = '';
        try {
            $id = Yii::app()->cnhutong_user->createCommand()
                ->select('id')
                ->from('user_member')
                ->where('user_id = :userId And memberId = :memberId',
                    array(
                        ':userId' => $userId,
                        ':memberId' => $memberId
                    )
                )
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $id;
    }
}