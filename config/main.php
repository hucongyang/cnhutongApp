<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'秦汉胡同app',
    'timeZone'=>'Asia/Shanghai',
	'defaultController'=>'Index',
	
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
        'application.extensions.*',
		'application.components.*',
	),

	'modules'=>array(
        // uncomment the following to enable the Gii tool
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'123456',                  
             // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'=>array('127.0.0.1','::1'),
        ),   
    ),
	
	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to use a MySQL database

		'cnhutong_user'=>array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=cnhutong_user',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '111111',
            'charset' => 'utf8',
            'class' =>  'CDbConnection',
            //'tablePrefix' => 'tbl_',
        ),
        // 对接数据库
        'cnhutong'=>array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=cnhutong',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '111111',
            'charset' => 'utf8',
            'class' =>  'CDbConnection',
            //'tablePrefix' => 'tbl_',
        ),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			//'errorAction'=>'site/error',
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'trace,info,profile,error, warning',
				),
				// uncomment the following to show log messages on web pages

				array(
					'class'=>'CWebLogRoute',
					'levels'=>'trace,info,profile,error, warning',
				),

			),
		),
		'curl' => array(
		        'class' => 'ext.Curl',
		        'options' => array() //.. additional curl options ../
		)
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);