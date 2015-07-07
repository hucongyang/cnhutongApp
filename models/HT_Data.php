<?php
 /*********************************************************
 * This is the model class for CnHutong Data
 * 
 * @package HT_Data
 * @author  Lujia
 *
 * @version 1.0 by Lujia @ 2015/05/19 创建类以及相关的操作
 ***********************************************************/

class HT_Data extends CActiveRecord
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
     * 返回管理员列表			getAdmins
     * @return $data			返回管理员列表
     *******************************************************/
    public function getAdmins($departmentId)
    {
    	$table_name = 'ht_member_fingerprint';
    	$data['admins'] = array();
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$result = $con_hutong->createCommand()
    		->select('member_id,finger_print')
    		->from($table_name)
    		->where('department_id=:DepartmentId and type=2', array(':DepartmentId' => $departmentId))
    		->query();
    
    		foreach ($result as $row) {
    			$admin = array();
    			$admin['memberId'] = $row['member_id'];
    			$name = HT_Member::model()->getNameByMemberId($admin['memberId']);
    			if ($name) {
    				$admin['name'] = $name;
    			} else {
    				$admin['name'] = 'noname';
    			}
    			$admin['fingerPrint'] = $row['finger_print'];
    			array_push($data['admins'], $admin);
    		}
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $data;
    }
    
    /*******************************************************
     * 返回学员列表				getStudents
     * @return $data			返回学员列表（获取前后一个月有课程的学员）
     *******************************************************/
    public function getStudents($departmentId)
    {
    	$date = date("Y-m-d");
    	$beginDate = date("Y-m-d", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
    	$endDate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
    	$table_name = 'ht_lesson_student';
    	$data['students'] = array();
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$result = $con_hutong->createCommand()
    		->select('distinct(student_id)')
    		->from($table_name)
    		->where('date>=:BeginDt and date<=:EndDt and department_id=:DepartmentId and step>=0',
    				array(':BeginDt' => $beginDate, ':EndDt' => $endDate, ':DepartmentId' => $departmentId))
    				->query();
    
    		foreach ($result as $row) {
    			$student = array();
    			$student['memberId'] = $row['student_id'];
    			$name = HT_Member::model()->getNameByMemberId($student['memberId']);
    			if ($name) {
    				$student['name'] = $name;
    			} else {
    				$student['name'] = 'noname';
    			}
    			$student['fingerPrint'] = HT_Member::model()->getFingerPrintByMemberId($student['memberId'], $departmentId);
    			array_push($data['students'], $student);
    		}
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $data;
    }
    
    /*******************************************************
     * 返回课程列表				getLessons
     * @return $data			返回当天起三天的课程列表
     *******************************************************/
    public function getLessons($departmentId)
    {
    	$date = date("Y-m-d");
    	$endDate = date("Y-m-d", strtotime("+1 day"));
    	$table_name = 'ht_lesson_student';
    	$data['lessons'] = array();
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$result = $con_hutong->createCommand()
    		->select('course_id,student_id,teacher_id,date,time,lesson_serial,lesson_arrange_id')
    		->from($table_name)
    		->where('date>=:BeginDate and date<=:EndDate and department_id=:DepartmentId and step>=0',
    				array(':BeginDate' => $date, ':EndDate' => $endDate, ':DepartmentId' => $departmentId))
    				->query();
    
    		foreach($result as $row) {
    			// 获取数据
    			$lesson = array();
    			$lesson['courseId'] = $row['course_id'];
    			$lesson['courseName'] = HT_Lesson::model()->getCourseName($lesson['courseId']);
    			$lessonCnt = HT_Lesson::model()->getLessonCnt($row['lesson_arrange_id']);
    			$lesson['lessonSerial'] = $row['lesson_serial'].'/'.$lessonCnt;
    			$lesson['lessonTime'] = $row['time'];
    			$lesson['teacherId'] = $row['teacher_id'];
    			$lesson['studentId'] = $row['student_id'];
    			// $lessonInfo['teacherName'] = HT_Member::model()->getNameByMemberId($lesson['teacherId']);
    			if($row['lesson_serial'] == 1) {
    				$lesson['lastTime'] = ' ';
    				$lesson['lessonContent'] = ' ';
    			} else {
    				$lessonData = HT_Lesson::model()->getLastLessonData($row['lesson_arrange_id'], 
    													$row['student_id'],  $row['teacher_id'], $departmentId);
    				$lesson['lastTime'] = $lessonData['lastTime'];
    				$lesson['lessonContent'] = $lessonData['lastLessonContent'];
    			}
    			array_push($data['lessons'], $lesson);
    		}
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $data;
    }
    
    /*******************************************************
     * 返回老师列表				getTeachers
     * @return $data			返回老师列表
     *******************************************************/
    public function getTeachers($departmentId)
    {
    	$date = date("Y-m-d");
    	$beginDate = date("Y-m-d", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
    	$endDate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
    	$table_name = 'ht_lesson_student';
    	$data['teachers'] = array();
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$result = $con_hutong->createCommand()
    		->select('distinct(teacher_id)')
    		->from($table_name)
    		->where('date>=:BeginDt and date<=:EndDt and department_id=:DepartmentId and step>=0',
    				array(':BeginDt' => $beginDate, ':EndDt' => $endDate, ':DepartmentId' => $departmentId))
    				->query();
    
    		foreach ($result as $row) {
    			$teacher = array();
    			$teacher['memberId'] = $row['teacher_id'];
    			$name = HT_Member::model()->getNameByMemberId($teacher['memberId']);
    			if ($name) {
    				$teacher['name'] = $name;
    			} else {
    				$teacher['name'] = 'noname';
    			}
    			$teacher['title'] = HT_Member::model()->getMemberTitle($row['teacher_id']);
    			array_push($data['teachers'], $teacher);
    		}
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $data;
    }
}