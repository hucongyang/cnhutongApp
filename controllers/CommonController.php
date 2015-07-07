<?php

class CommonController extends ApiPublicController
{
    /**
     * 获取校区列表      getAllSchools()
     * @param userId int 用户id  非必填
     * @return $result          调用返回结果
     * @return $msg             调用返回结果说明
     * @return $data             调用返回数据
     */
    public function actionGetAllSchools()
    {
        // 非必须
        $userId = Yii::app()->request->getParam('userId', NULL);

        $data = ComDepartment::getAllSchools();
        if(!$data) {
            $this->_return('MSG_ERR_UNKOWN');
        }
        $this->_return('MSG_SUCCESS', $data);
    }

    /**
     * 获取校区详细列表      getSchoolInfo()
     * @param userId int 用户id  非必填
     * @param departmentId int 校区对应ID 必填
     * @return $result          调用返回结果
     * @return $msg             调用返回结果说明
     * @return $data             调用返回数据
     */
    public function actionGetSchoolInfo()
    {
        if(!isset($_REQUEST['departmentId']))
        {
            $this->_return('MSG_ERR_LESS_PARAM');
        }
        // 非必须
        $userId = Yii::app()->request->getParam('userId', NULL);
        // 必须
        $departmentId = Yii::app()->request->getParam('departmentId', NULL);

        $data = array();
        $school = ComDepartment::getSchoolInfo($departmentId);
        if(!$school) {
            $this->_return('MSG_ERR_FAIL_DEPARTMENT');
        }
        $data['school'] = $school;
        $pictures = ComDepartmentPicture::getSchoolInfoPicture($departmentId);
        if(!$pictures) {
            $data['pictures'] = [''];
//            $this->_return('MSG_ERR_FAIL_DEPARTMENT');
        }
        $data['pictures'] = $pictures;
        $this->_return('MSG_SUCCESS', $data);
    }
}