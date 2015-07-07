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
     * @param $userId
     * @return array|bool
     */
    public static function getMembers($userId) {
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
                    'id' => $row['id'],
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
}