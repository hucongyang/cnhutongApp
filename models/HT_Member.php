<?php
 /*********************************************************
 * This is the model class for CnHutong Member
 * 
 * @package HT_Member
 * @author  Lujia
 *
 * @version 1.0 by Lujia @ 2015/05/07 创建类以及相关的操作
 ***********************************************************/

class HT_Member extends CActiveRecord
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
    
	/*******************************************************
	 * 学员/综合行政指政设置		setFingerSign
	 * @return $retCode			返回结果
	 *******************************************************/
	public function setFingerPrint($memberId, $fingerPrint, $type, $departmentId, $addTime)
    {
        $time = date("Y-m-d H:i:s");
        $table_name = 'ht_member_fingerprint';
        try {
            $con_hutong = Yii::app()->db_hutong;
            $transaction = $con_hutong->beginTransaction();
            $id = $con_hutong->createCommand()
                ->select('id')
                ->from($table_name)
                ->where('member_id=:MemberId And department_id=:DepartmentId And type=:Type',
                    array(':MemberId' => $memberId, 'DepartmentId' => $departmentId, ':Type' => $type))
                ->queryScalar();
            if ($id) {
                // update the finger print
                $con_hutong->createCommand()->update($table_name,
                    array('update_ts' => $time,
                        'finger_print' => $fingerPrint),
                    'id=:Id', array(':Id' => $id));
            } else {
                // insert the new finger print
                $table_name = 'ht_member_fingerprint';
                $con_hutong->createCommand()->insert($table_name,
                    array('member_id' => $memberId,
                        'type' => $type,
                        'finger_print' => $fingerPrint,
                        'department_id' => $departmentId,
                        'create_ts' => $time,
                        'send_ts' => $addTime));
            }
            // 提交事务
            $transaction->commit();
        } catch (Exception $e) {
            error_log($e);
            // 回滚事务
            $transaction->rollback();
            return false;
        }
        return true;
    }
    
    /*******************************************************
     * 删除综合行政				removeAdmin
     * @return $retCode			返回结果
     *******************************************************/
    public function removeAdmin($memberId, $departmentId)
    {
    	$time = date("Y-m-d H:i:s");
    	$table_name = 'ht_member_fingerprint';
    	$result = 0;
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$transaction = $con_hutong->beginTransaction();
    		$id = $con_hutong->createCommand()
				    		 ->select('id')
				    		 ->from($table_name)
				    		 ->where('member_id=:MemberId And department_id=:DepartmentId And type=2',
				    				array(':MemberId' => $memberId, 'DepartmentId' => $departmentId))
				    		 ->queryScalar();
    		if ($id) {
    			// remove the admin info
    			$con_hutong->createCommand()->delete($table_name, 'id=:Id', array(':Id' => $id));
    		} else {
    			// return error
    			$result = 1;
    		}
    		// 提交事务
    		$transaction->commit();
    	} catch (Exception $e) {
    		error_log($e);
    		// 回滚事务
    		$transaction->rollback();
    		return 2;
    	}
    	return $result;
    }

    /*******************************************************
     * 检查管理员是否存在			checkAdminExist
     * @return result			检查管理员是否存在
     *******************************************************/
    public function checkAdminExist($adminId, $departmentId)
    {
    	$table_name = 'ht_member_fingerprint';
    	$result = 0;
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$id = $con_hutong->createCommand()
				    		->select('id')
				    		->from($table_name)
				    		->where('member_id=:MemberId and department_id=:DepartmentId and type=2',
				    				array(':MemberId' => $adminId, ':DepartmentId' => $departmentId))
				    				->queryScalar();
    		if(!$id){
    			$result = 1;
    		}
    	} catch (Exception $e) {
    		error_log($e);
    		$result = 1;
    	}
    	return $result;
    }
    
    /*******************************************************
     * 根据memberId返回对应姓名		getNameByMemberId
     * @return name			    	返回对应姓名
     *******************************************************/
    public function getNameByMemberId($memberId)
    {
        $table_name = 'ht_member';
        $name = NULL;
        try {
            $con_hutong = Yii::app()->db_hutong;
            $name = $con_hutong->createCommand()
                ->select('name')
                ->from($table_name)
                ->where('id=:MemberId', array(':MemberId' => $memberId))
                ->queryScalar();
        } catch (Exception $e) {
            error_log($e);
        }
        return $name;
    }
    
    /*******************************************************
     * 返回对应Title					getMemberTitle
     * @return title		    	根据memberId返回对应Title
     *******************************************************/
    public function getMemberTitle($memberId)
    {
    	$table_name = 'ht_member';
    	$title = NULL;
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$title = $con_hutong->createCommand()
    		->select('title')
    		->from($table_name)
    		->where('id=:MemberId', array(':MemberId' => $memberId))
    		->queryScalar();
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $title;
    }
    
    /*******************************************************
     * 根据memberId返回对应指纹		getFingerPrintByMemberId
     * @return fingerPrint	    	根据memberId返回对应指纹
     *******************************************************/
    public function getFingerPrintByMemberId($memberId, $departmentId)
    {
    	$table_name = 'ht_member_fingerprint';
    	$fingerPrint = '';
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$fingerPrint = $con_hutong->createCommand()
						    		->select('finger_print')
						    		->from($table_name)
						    		->where('member_id=:MemberId and department_id=:DepartmentId', 
						    				array(':MemberId' => $memberId, ':DepartmentId' => $departmentId))
						    		->queryScalar();
    		// echo $fingerPrint; exit;
    		if(!$fingerPrint){
    			$fingerPrint = '';
    		}
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $fingerPrint;
    }

    /*******************************************************
     * 根据用户姓名返回对应的memberId		getMemberId
     * @param $type               		1-学员  2-综合行政
     * @return $data			    	返回对应的MemberId数组
     *******************************************************/
    public function getMemberId($name, $type, $departmentId)
    {
        $data['members'] = array();
        $table_name = 'ht_member';
        if ($type == 1) {
            // 学员
            try {
                $con_hutong = Yii::app()->db_hutong;
                $result = $con_hutong->createCommand()
                    ->select('id,name,mobile,department_id')
                    ->from($table_name)
                    ->where('name=:Name and role=\'student\' and step>=0',
                            array(':Name' => $name))
                    ->query();

                if($result->rowCount == 0) {
                    // 如果没有本校区的数据，则从所有信息中去查找该学员的数据
                    $result = $con_hutong->createCommand()
                        ->select('id,name,mobile,department_id')
                        ->from($table_name)
                        ->where('name=:Name and role=\'student\' and step>=0',
                            array(':Name' => $name))
                        ->query();
                }
                foreach ($result as $row) {
                    $member  = array();
                    $member['memberId'] = $row['id'];
                    $member['name'] = $row['name'];
                    $member['mobile'] = $row['mobile'];
                    $member['departmentId'] = $row['department_id'];
                    array_push($data['members'], $member);
                }
            } catch (Exception $e) {
                error_log($e);
            }
        } else if ($type) {
            // 综合行政
            try {
                $con_hutong = Yii::app()->db_hutong;
                $result = $con_hutong->createCommand()
                    ->select('id,name,mobile,department_id')
                    ->from($table_name)
                    ->where('name=:Name and department_id=:DepartmentId and role<>\'student\' and step>=0',
                        array(':Name' => $name, ':DepartmentId' => $departmentId))
                    ->query();
                if($result->rowCount == 0) {
                    // 如果没有本校区的数据，则从所有信息中去查找该学员的数据
                    $result = $con_hutong->createCommand()
                        ->select('id,name,mobile,department_id')
                        ->from($table_name)
                        ->where('name=:Name and role<>\'student\' and step>=0',
                            array(':Name' => $name))
                        ->query();
                }
                foreach ($result as $row) {
                    $member = array();
                    $member['memberId'] = $row['id'];
                    $member['name'] = $row['name'];
                    $member['mobile'] = $row['mobile'];
                    $member['departmentId'] = $row['department_id'];
                    array_push($data['members'], $member);
                }
            } catch (Exception $e) {
                error_log($e);
            }
        } else {
            return null;
        }
        return $data;
    }
}