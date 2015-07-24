<?php
/*********************************************************
 * 接口公用方法
 * 
 * @author  Lujia
 * @version 1.0 by Lujia @ 2015/05/07 创建
 ***********************************************************/
class ApiPublicController extends Controller 
{


    public function __construct()
    {
        // 检查参数
//        if(!isset($_REQUEST['department']) || !isset($_REQUEST['mac']) || !isset($_REQUEST['apiVer']))
//            {
//            $this->_return('MSG_ERR_LESS_PARAM');
//        }
        // $_department_id = 1;
        // TODO : 此处考虑记录访问LOG

        // 秦汉胡同APP log公共参数 检查参数
//        if(!isset($_REQUEST['version']) || !isset($_REQUEST['deviceId']) || !isset($_REQUEST['platform']) || !isset($_REQUEST['channel'])
//            || !isset($_REQUEST['appVersion']) || !isset($_REQUEST['osVersion']) || !isset($_REQUEST['appId'])) {
//            $this->_return('MSG_ERR_LESS_PARAM');
//        }
    }

	/*******************************************************
	 * API调用返回对应JSON数据包
	 * @author Lujia
	 * @create 2015/05/07
	 * @modify 2015/05/07   修改为通用返回接口
	 *******************************************************/
    public function _return($error_code, $data=NULL) 
	{
		require 'ErrorCode.php';
		if(!$data)
		{
			exit(json_encode(array('msg' => $_error_code[$error_code][1], 
							   'result' => $_error_code[$error_code][0])));
		}
		else
		{
			exit(json_encode(array('msg' => $_error_code[$error_code][1], 
							   'result' => $_error_code[$error_code][0],
							   'data' => $data)));
		}
    }
    

    
    /*******************************************************
	 * 返回渠道是否有支付宝显示
	 * @author Lujia
	 * @create 2014/03/19
	 *******************************************************/
    public function _get_channel_display($channel) 
	{
		require 'Channel.php';
		$channel = 'Ch'. $channel;
		$ret_data = 1;
		if(array_key_exists($channel, $_channel_display))
		{
			$ret_data = $_channel_display[$channel][0];
		}
		return $ret_data;
    }
    
    /*******************************************************
	 * 返回渠道最新版本号
	 * @author Lujia
	 * @create 2014/03/19
	 *******************************************************/
    public function _get_channel_version($channel) 
	{
		require 'Channel.php';
		$channel = 'Ch'. $channel;
		$ret_data = NULL;
		if(array_key_exists($channel, $_channel_display))
		{
			$ret_data = $_channel_display[$channel][1];
		}
		return $ret_data;
    }
    
    /*******************************************************
	 * 返回渠道最新版本下载地址
	 * @author Lujia
	 * @create 2014/03/19
	 *******************************************************/
    public function _get_channel_download($channel) 
	{
		require 'Channel.php';
		$channel = 'Ch'. $channel;
		$ret_data = '';
		if(array_key_exists($channel, $_channel_display))
		{
			$ret_data = $_channel_display[$channel][2];
		}
		return $ret_data;
    }
	
	/*******************************************************
	 * 检查是否为合法的IP地址
	 * @author Lujia
	 * @create 2013/12/23
	 *******************************************************/
    public function isIPAddress($ip) 
	{
		// TODO：这个IP地址不能完全的进行匹配
		$ip_rules = "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/";
		if (preg_match($ip_rules, $ip)) 
		{
            return true;
        } 
		else 
		{
            return false;
        }
    }
	
    /*******************************************************
	 * 检查是否为合法的手机号码
	 * @author Lujia
	 * @create 2013/12/23
	 *******************************************************/
    public function isMobile($mobile) 
	{
        if (preg_match("/^1[3|5|8]\d{9}$/", $mobile)) 
		{
            return true;
        } 
		else 
		{
            return false;
        }
    }
	
	/*******************************************************
	 * 检查是否为合法的邮箱地址
	 * @author Lujia
	 * @create 2013/12/24
	 *******************************************************/
    public function isEmail($email) 
	{
		$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if (preg_match($pattern, $email)) 
		{
            return true;
        } 
		else 
		{
            return false;
        }
    }
	
	/*******************************************************
	 * 检查密码规则
	 * @author Lujia
	 * @create 2013/12/25
	 *******************************************************/
    public function isPasswordValid($pass) 
	{
		$pattern = "/^[\w~!@#$%^&*]{6,20}$/";
        if (preg_match($pattern, $pass)) 
		{
            return true;
        } 
		else 
		{
            return false;
        }
    } 

	/*******************************************************
	 * 检查是否含有屏蔽词
	 * @author Lujia
	 * @create 2013/12/30
	 *******************************************************/
    public function isExistShieldWord($source) 
	{
		$words = Yii::app()->params['shield_word'];
		// 遍历检测
		for($i = 0, $k = count($words); $i < $k; $i++)
		{
			// 如果此数组元素为空则跳过此次循环
			if($words[$i]=='')
			{
				continue;
			}
			
			// 如果检测到关键字，则返回匹配的关键字,并终止运行
			if(strpos($source, trim($words[$i])) !== false)
			{
				return true;
			}
		}
		return false;
    } 
	
	/*******************************************************
	 * 加密算法
	 * @author Lujia
	 * @create 2013/12/24
	 *******************************************************/
	public function _encrypt($data, $key)
	{
		$key = md5($key);
		$x  = 0;
		$len = strlen($data);
		$l  = strlen($key);
		$char = '';
		$str = '';
		for ($i = 0; $i < $len; $i++)
		{
			if ($x == $l) 
			{
				$x = 0;
			}
			$char .= $key{$x};
			$x++;
		}
		for ($i = 0; $i < $len; $i++)
		{
			$str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
		}
		return base64_encode($str);
	}
	
	/*******************************************************
	 * 解密算法
	 * @author Lujia
	 * @create 2013/12/24
	 *******************************************************/
	public function _decrypt($data, $key)
	{
		$key = md5($key);
		$x = 0;
		$data = base64_decode($data);
		$len = strlen($data);
		$l = strlen($key);
		$char = '';
		$str = '';
		for ($i = 0; $i < $len; $i++)
		{
			if ($x == $l) 
			{
				$x = 0;
			}
			$char .= substr($key, $x, 1);
			$x++;
		}
		for ($i = 0; $i < $len; $i++)
		{
			if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
			{
				$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
			}
			else
			{
				$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
			}
		}
		return $str;
	}
	
	/*******************************************************
	 * 获取密码加解密密钥
	 * @author Lujia
	 * @create 2013/12/24
	 *******************************************************/
	public function getCryptKey()
	{
		return 'mokun';
	}
	
	/*******************************************************
	 * 获取数据验证密钥
	 * @author Lujia
	 * @create 2014/01/23
	 *******************************************************/
	public function getSignKey()
	{
		return 'SHMokun9418';
	}

	/*******************************************************
	 * 获取连接IP
	 * @author Lujia
	 * @create 2013/12/26
	 *******************************************************/	
	public function getClientIP()  
	{  
		if (getenv("HTTP_CLIENT_IP"))  
		{
			$ip = getenv("HTTP_CLIENT_IP");  
		}
		else if(getenv("HTTP_X_FORWARDED_FOR"))  
		{
			$ip = getenv("HTTP_X_FORWARDED_FOR");  
		}
		else if(getenv("REMOTE_ADDR")) 
		{
			$ip = getenv("REMOTE_ADDR");  
		}
		else 
		{
			$ip = "Unknow";  
		}
		return $ip;  
	}
	
	/*******************************************************
	 * http post请求
	 * @author Lujia
	 * @create 2011/01/08
	 *******************************************************/	
	function actionCurl($remote_server, $post_string)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $remote_server);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		//  curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		$result = curl_exec($ch);
		return $result;
	} 
	
	/*******************************************************
	 * 版本号转换
	 * @author Lujia
	 * @create 2011/01/15
	 *******************************************************/	
	function convertVersion($version)
	{
		$ver_num = explode(".", $version);
		return $ver_num[0] * 10000 + $ver_num[1] * 100 + $ver_num[2];
	} 
	
    /**
     *  记录日志     未完成
     * @param array $arr
     */
    public function _writeLog($arr) {
        $fileDir = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
        if (!is_dir($fileDir)) {
            mkdir($fileDir, 0755, true);
        }
        $fileName = $fileDir . 'data.log';
        file_put_contents($fileName, $arr, FILE_APPEND);
    }

    /**
     *  计算字符串长度   未完成
     * @param string $str
     * @return int num
     */
    public function strlen_str($str) {
        $len = strlen($str);
        $i = 0;
        while ($i < $len) {
            if (preg_match("/^[" . chr(0xa1) . "-" . chr(0xff) . "]+$/", $str[$i])) {
                $i+=2;
            } else {
                $i+=1;
            }
        }
        return $i;
    }

    /**
     * 判断数组（多维）数值是否为空
     * @param null $arr
     * @return bool
     */
    public static function array_is_null($arr = null)
    {
        if(is_array($arr)) {
            foreach($arr as $k => $v) {
                if($v && is_array($v)) {
                    return false;
                }
                $t = self::array_is_null($v);
                if(!$t) {
                    return false;
                }
            }
            return true;
        } elseif (!$arr) {
            return true;
        } else {
            return false;
        }
    }

}