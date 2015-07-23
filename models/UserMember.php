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
                ->where('user_id = :userId And status in (0,1)', array(':userId' => $userId))
                ->queryAll();
            if(!$member_model) {
                return false;
            }
            foreach($member_model as $row) {
                $aMembers[] = array(
                    'name' => self::getMemberNameByMemberId($row['member_id']),
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
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 验证token
            $userToken = UserToken::model()->IsToken($userId, $token);
            if(!$userToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }
            // 验证学员ID数量，目前最多绑定3个
            $countMemberId = self::getBindMemberNum($userId);
            if($countMemberId <=0 || $countMemberId >= 3) {
                return 20006;
            }
            //口令salt 不能为空
            if(!$salt) {
                return 40004;       // MSG_ERR_NULL_SALT
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

            //members
            $data['members'] = self::getMembers($userId);
            if(!$data['members']) {
                $data['members'] = "尚未绑定学员id";
            }

        } catch (Exception $e) {
            error_log($e);
        }
        return $data;
    }

    /**
     * 用户解除绑定学员id
     * @param $userId
     * @param $token
     * @param $memberId
     * @return array
     */
    public function removeMember($userId, $token, $memberId)
    {
        $data = array();
        try {
            // 验证userId
            $user = User::model()->IsUserId($userId);
            if(!$user) {
                return 10010;       // MSG_ERR_FAIL_USER
            }
            // 验证token
            $userToken = UserToken::model()->IsToken($userId, $token);
            if(!$userToken) {
                return 10009;       // MSG_ERR_FAIL_TOKEN
            }
            // 验证要删除的memberId 是否存在
            $userMemberId = self::IsExistMemberId($userId, $memberId);
            if(!$userMemberId) {
                return 40003;       // MSG_ERR_FAIL_MEMBER
            }
            // 验证通过后，解除学员id的绑定
            $delete_member = Yii::app()->cnhutong_user->createCommand()
                ->update('user_member',
                    array(
                        'status' => 9
                    ),
                    'user_id = :userId And member_id = :memberId',
                    array(
                        ':userId' => $userId,
                        ':memberId' => $memberId
                    )
                );

            //members
            $data['members'] = self::getMembers($userId);
            if(!$data['members']) {
                $data['members'] = "尚未绑定学员id";
            }

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
                ->where('user_id = :userId And member_id = :memberId',
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

    /**
     * 输入：学员特有memberID
     * 输出：学员姓名
     * @param $memberId
     * @return array
     */
    public function getMemberNameByMemberId($memberId)
    {
        $name = '';
        try {
            $name = Yii::app()->cnhutong->createCommand()
                ->select('name')
                ->from('ht_member')
                ->where('id = :memberId', array(':memberId' => $memberId))
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $name;
    }

    /**
     * 输入：App唯一标识ID
     * 输出：该ID绑定的memberID数量
     * @param $userId
     * @return int
     */
    public function getBindMemberNum($userId)
    {
        $count = 0;
        try {
            $count = Yii::app()->cnhutong_user->createCommand()
                ->select('count(id)')
                ->from('user_member')
                ->where('user_id = :userId And status = 1', array(':userId' => $userId))
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $count;
    }
}