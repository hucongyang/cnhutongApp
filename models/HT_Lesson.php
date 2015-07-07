<?php
 /*********************************************************
 * This is the model class for CnHutong Lesson
 * 
 * @package HT_Lesson
 * @author  Lujia
 *
 * @version 1.0 by Lujia @ 2015/05/19 创建类以及相关的操作
 ***********************************************************/

class HT_Lesson extends CActiveRecord
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
     * 获取学员当天所有课程       	getStudnetLessonIds
     * @return $lessons			返回学员当天课程列表
     *******************************************************/
    public function getStudnetLessonIds($memberId, $departmentId, $date)
    {
    	$table_name = 'ht_lesson_student';
    	$lessons = array();
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$result = $con_hutong->createCommand()
	    		->select('id,teacher_id,lesson_serial,course_id,time,lesson_arrange_id')
	    		->from($table_name)
	    		->where('date=:Date and student_id=:StudentId and department_id=:DepartmentId and step>=0',
	    				array(':Date' => $date, ':StudentId' => $memberId, ':DepartmentId' => $departmentId))
	    				->query();
    
    		foreach ($result as $row) {
    			$lesson = array();
    			$lesson['lessonStudentId'] = $row['id'];
    			$lesson['teacherId'] = $row['teacher_id'];
    			$lesson['lessonSerial'] = $row['lesson_serial'];
    			$lesson['courseId'] = $row['course_id'];
    			$lesson['time'] = $row['time'];
    			$lesson['lessonArrangeId'] = $row['lesson_arrange_id'];
    			array_push($lessons, $lesson);
    		}
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $lessons;
    }
    
    /*******************************************************
     * 获取课程名称       	    getCourseName
     * @return $lessonName		返回课程名称
     *******************************************************/
    public function getCourseName($courseId)
    {
    	$table_name = 'ht_course';
    	$lessons = array();
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$courseName = $con_hutong->createCommand()
	    		->select('course')
	    		->from($table_name)
	    		->where('id=:CourseId and step>=0',
	    				array(':CourseId' => $courseId))
	    				->queryScalar();
    		if(!$courseName) {
    			$courseName = '';
    		}
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $courseName;
    }
    
    /*******************************************************
     * 获取上次课程内容       	getLastLessonData
     * @return $data		获取上次课程内容     
     *******************************************************/
    public function getLastLessonData($lessonArrageId, $memberId, $teacherId, $departmentId)
    {
    	$date = date("Y-m-d");
    	$table_name = 'ht_lesson_student';
    	$lesson = array();
    	try {
    		$con_hutong = Yii::app()->db_hutong;
//     		$result = $con_hutong->createCommand()
//     		->select('date,time')
//     		->from($table_name)
//     		->where('lesson_arrange_id=:ArrangeId and lesson_serial=:SerialId and step>=0',
//     				array(':ArrangeId' => $lessonArrangeId, ':SerialId' => $lessonSerial))
//     				->queryRow();
    		
    		$data = $con_hutong->createCommand()
    		->select('date,time,id,lesson_serial,lesson_arrange_id,course_id')
    		->from($table_name)
    		->where('lesson_arrange_id=:ArrangeId AND department_id=:DepartmentId AND date<:Date '.
    				'AND student_id=:MemberId AND teacher_id=:TeacherId and step=0 and status_id>0',
    				array(':ArrangeId' => $lessonArrageId, ':DepartmentId' => $departmentId, 'Date' => $date,
    						':MemberId' => $memberId, ':TeacherId' => $teacherId))
    						->order('date desc')
    						->limit(1)
    						->queryRow();
    		 
    		$lesson['lastTime'] = $data['date'].' '.$data['time'];
    		$lesson['lessonStudentId'] = $data['id'];
    		// $lesson['courseId'] = $data['course_id'];
    		 
    		// $lessonCnt = $this->getLessonCnt($data['lesson_arrange_id']);
    		// $lesson['courseRange'] = $data['lesson_serial'].'/'.$lessonCnt;
    		$lesson['lastLessonContent'] = $this->getLessonContent($lesson['lessonStudentId']);
    		
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $lesson;
    }
    
    /*******************************************************
     * 获取课程的课程ID          	getLessonStudentId
     * @return $lessonStudentId		获取课程的课程ID
     *******************************************************/
    public function getLessonStudentId($lessonArrangeId, $lessonSerial)
    {
    	$table_name = 'ht_lesson_student';
    	$lessonStudentId = NULL;
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$lessonStudentId = $con_hutong->createCommand()
    		->select('id')
    		->from($table_name)
    		->where('lesson_arrange_id=:ArrangeId and lesson_serial=:SerialId and step>=0',
    				array(':ArrangeId' => $lessonArrangeId, ':SerialId' => $lessonSerial))
    				->queryScalar();
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $lessonStudentId;
    }
    
    /*******************************************************
     * 获取课程对应的总课时数          	getLessonCnt
     * @return $lessonCnt			获取课程对应的总课时数
     *******************************************************/
    public function getLessonCnt($lessonArrangeId)
    {
    	$table_name = 'ht_lesson_arrange_rules';
    	$lessons = array();
    	try {
    		$con_hutong = Yii::app()->db_hutong;
    		$lessonCnt = $con_hutong->createCommand()
    		->select('cnt')
    		->from($table_name)
    		->where('id=:ArrangeId', array(':ArrangeId' => $lessonArrangeId))
    		->queryScalar();
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $lessonCnt;
    }
    
    /*******************************************************
     * 组合课程时间		          	comLessonTime
     * @return $lessonTime		 	组合课程时间
     *******************************************************/
    public function comLessonTime($lessonTime)
    {
    	$startHour = 8 + ($lessonTime + 1) / 2;
    	$endHour = 10 + $lessonTime / 2;
    	if($lessonTime % 2 == 1) {
    		$startMin = 0;
    		$endMin = 30;
    	} else {
    		$startMin = 30;
    		$endMin = 0;
    	}
    	
    	$lessonTime = sprintf("%d:%02d-%d:%02d", $startHour, $startMin, $endHour, $endMin);
    	return $lessonTime;
    }
    
    /*******************************************************
     * 获取是否已签到过          			checkSignExistOrNot
     * @return $result			 	获取是否已签到过
     *******************************************************/
    public function checkSignExistOrNot($memberId, $teacherId, $time, $departmentId)
    {
    	$date = date("Y-m-d");
    	$id = NULL;
    	$table_name = 'ht_log_member_sign';
    	try {
    	 	$con_hutong = Yii::app()->db_hutong;
    		$id = $con_hutong->createCommand()
    				  	->select('id')
    					->from($table_name)
    			 		->where('member_id=:MemberId AND department_id=:DepartmentId AND teacher_id=:TeacherId AND date=:Date AND time=:Time', 
    			 				array(':MemberId' => $memberId, 'DepartmentId' => $departmentId,
    			 						 ':TeacherId' => $teacherId, ':Date' => $date, ':Time' => $time))
    				    ->queryScalar();
    	} catch (Exception $e) {
    	    error_log($e);
    	}
    	return $id;
    }
    
    /*******************************************************
     * 获取加课课程对应的信息          	getAddLessonInfo
     * @return $lesson			 	获取加课课程对应的信息
     *******************************************************/
    public function getAddLessonInfo($departmentId, $memberId, $teacherId, $courseId)
    {
    	$date = date("Y-m-d");
    	$lesson = array();
    	$lesson['result'] = 0;
    	
    	$table_name = 'ht_lesson_student';
    	try {
    		// 检查学生是否有老师有教学关系
    		$con_hutong = Yii::app()->db_hutong;
    		$lesson['lessonArrangeId'] = $con_hutong->createCommand()
			    		->select('lesson_arrange_id')
			    		->from($table_name)
			    		->where('department_id=:DepartmentId AND student_id=:MemberId AND teacher_id=:TeacherId AND step>=0', 
			    				array(':DepartmentId' => $departmentId, ':MemberId' => $memberId, ':TeacherId' => $teacherId))
			    		->limit(1)
			    		->queryScalar();
    		if(!$lesson['lessonArrangeId'] || $lesson['lessonArrangeId'] == 0) {
    			$lesson['result'] = 1;
    		} else {
    			// 检查学员的课程是否已全部消耗
    			$tableNameCheck = 'ht_lesson_arrange_rules';
    			$lessonArrange = $con_hutong->createCommand()
			    		->select('lesson_finished_cnt,cnt')
			    		->from($tableNameCheck)
			    		->where('id=:ArrangeId AND step>=0', array(':ArrangeId' => $lesson['lessonArrangeId']))
			    		->queryRow();
    			if($lessonArrange['lesson_finished_cnt'] >= $lessonArrange['cnt']) {
    				$lesson['result'] = 2;
    			} else {
	    			$data = $con_hutong->createCommand()
				    		->select('date,time,id,lesson_serial,lesson_arrange_id,course_id')
				    		->from($table_name)
				    		->where('lesson_arrange_id=:ArrangeId AND department_id=:DepartmentId AND date<:Date '.
				    					'AND student_id=:MemberId AND teacher_id=:TeacherId and step=0 and status_id>0', 
				    				array(':ArrangeId' => $lesson['lessonArrangeId'], ':DepartmentId' => $departmentId, 'Date' => $date,
				    						':MemberId' => $memberId, ':TeacherId' => $teacherId))
				    		->order('date desc')
				    		->limit(1)
				    		->queryRow();
	    			
	    			$lesson['lastTime'] = $data['date'].' '.$data['time'];
	    			$lesson['lessonStudentId'] = $data['id'];
	    			$lesson['courseId'] = $data['course_id'];
	    			
	    			$lessonCnt = $this->getLessonCnt($data['lesson_arrange_id']);
	    			$lesson['courseRange'] = $data['lesson_serial'].'/'.$lessonCnt / 2;
	    			$lesson['lastLessonContent'] = $this->getLessonContent($lesson['lessonStudentId']);
    			}
    		}
    	} catch (Exception $e) {
    		error_log($e);
    	}
    	return $lesson;
    }
    
    /*******************************************************
     * 获取课程的教学内容          		getLessonContent
     * @return $lessonContent		获取课程的教学内容
     *******************************************************/
    public function getLessonContent($lessonStudentId)
    {
    	// 获取时先获取ID和mytopic如果mytopic没有内容，再取标准内容
    	$lessonContent = '';
    	$lessons = array();
    	try {
			$con_hutong = Yii::app()->db_hutong;
			$table_name = 'ht_lesson_student_topic';
			$result = $con_hutong->createCommand()
    	                		->select('lesson_topic_id,mytopic')
    	                		->from($table_name)
    	               			->where('lesson_student_id=:LessonId and step>=0',
    	                   			array(':LessonId' => $lessonStudentId))
    	                		->query();
			foreach ($result as $row) {
				$lesson = array();
				if($row['mytopic'] && $row['mytopic'] != '') {
					$lessonContent = $lessonContent.$row['mytopic'].'8424';
				} else {
					$table_name = 'ht_lesson_topic';
					$topic = $con_hutong->createCommand()
								->select('lesson_topic_id,mytopic')
								->from($table_name)
								->where('id=:TopicId and step>=0',
									array(':TopicId' => $row['lesson_topic_id']))
								->queryScalar();
					$lessonContent = $lessonContent.$topic.'8424';
				}
			}
		} catch (Exception $e) {
    		error_log($e);
    	}
    	return $lessonContent;
    }
}