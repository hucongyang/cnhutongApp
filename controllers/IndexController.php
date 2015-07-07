<?php
class IndexController extends ApiPublicController 
{
    public function actionIndex()
	{
        echo "Test The YII FrameWork" . "\n";
        echo date( "M-d-Y", mktime(0,0,0,13,33,1997) ) . "\n";
        echo $nowTime = date("Y-m-d H-i-s") . "\n";
        echo strtotime("now");
    }
	
	public function actionTest()
	{
        echo $this->convertVersion('2.0.0');
        for($i = 1; $i < 23; $i++) {
        	echo HT_Lesson::model()->comLessonTime($i);
        }
    }
    
    /*******************************************************
	 * 获取基本信息  actionGetInfo
	 *
	 * @param $type			// 用户设备类型(1-ios 2-android)	 
	 * @param $game			// 来源游戏
	 * @param $version		// 游戏当前的版本号
	 *
	 * @return $result		// 调用返回结果
	 * @return $msg			// 调用返回结果说明
	 * @return $url			// URL前缀
	 * @return $version		// 是否为最新版本（0表示不需要更新，1表示需要更新）
	 * @return $download	// 下载地址
	 *
	 * 说明：为了方便切换版本及动态提示更新等内容，
	 * 		 用户在进入游戏后，首先调用该接口，获取基本信息。
	 *******************************************************/
	 public function actionGetInfo()
	 {
	 	$type = trim(Yii::app()->request->getParam('type', 2));
		$game = trim(Yii::app()->request->getParam('game', 1));
		$version = trim(Yii::app()->request->getParam('version', '0.0.1'));
		$channel = trim(Yii::app()->request->getParam('channel', 2000));
		
		$data = array();
		$data['url'] = Yii::app()->params['url_base'];
		
		switch($game)
		{
			case 1 :
			{
				$update_version = $this->_get_channel_version($channel);
				if($update_version == NULL)
				{
					$data['version'] = 0;
					$data['download'] = Yii::app()->params['sh_android_download_url'];
					$data['chargeType'] = 0;
				}
				else
				{
					if($this->convertVersion($version) < $this->convertVersion($update_version))
					{
						$data['version'] = 1;
					}
					else
					{
						$data['version'] = 0;
					}
					$data['download'] = $this->_get_channel_download($channel);
				$data['chargeType'] = $this->_get_channel_display($channel);
				}
				break;
			}
			default : 
			{
				$data['version'] = 0;
				$data['download'] = Yii::app()->params['sh_android_download_url'];
				$data['chargeType'] = 0;
				break;
			}
		}
		
		// 发送返回值
		$this->_return('MSG_SUCCESS', $data);
	 }
}