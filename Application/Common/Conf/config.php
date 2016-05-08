<?php
$config=array (
		'DB_TYPE'            => 'mysql', // 数据库类型
		'DB_HOST'            => 'localhost', // 数据库服务器地址
		'DB_NAME'            => 'searchsys', // 数据库名称
		'DB_USER'            => 'root', // 数据库用户名
		'DB_PWD'             => 'root', // 数据库密码
		'DB_PORT'            => '3306', // 数据库端口
		'DB_PREFIX'          => 'sc_', // 数据表前缀
		'DB_CHARSET'         => 'utf8', // 网站编码
		'VAR_PAGE'           => 'p',
		'URL_HTML_SUFFIX'    => 'html',  // URL伪静态后缀设置
		'SHOW_PAGE_TRACE'    => false,    // 显示页面Trace信息
		'URL_MODEL'          => '2', //URL模式
		'SESSION_AUTO_START' => true, //是否开启session
		'URL_CASE_INSENSITIVE' =>false, //URL访问不再区分大小写
		'APP_FILE_CASE'  =>   true, // 是否检查文件的大小写 对Windows平台有效
		'MODULE_ALLOW_LIST' => array (
				'Home',
				'Admin'
		),
		'DEFAULT_MODULE' => 'Home',//默认模块
);
return $config;