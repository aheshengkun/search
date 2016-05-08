<?php
return array(
	'TMPL_ACTION_ERROR'   => 'Public:error',
	'TMPL_ACTION_SUCCESS' => 'Public:success', //默认成功跳转对应的模板文件
	'RBAC_SUPPERADMIN_ID' => '1', //超级管理员的用户ID
	'RBAC_SUPERADMIN'     => 'admin', //超级管理员名称
	'ADMIN_AUTH_KEY'      => 'superadmin',//超级管理员识别
	'USER_AUTH_ON'        => true,//是否开启验证
	'USER_AUTH_TYPE'      => 2,//验证类型（1为登录验证 2为实时验证）
	'USER_AUTH_KEY'       => 'id',//用户验证识别号
	'NOT_AUTH_MODULE'     => 'Index,Public',//无需验证的控制器
	'NOT_AUTH_ACTION'     => '',//无需验证的方法
	'USER_AUTH_MODEL'     => 'user',	// 默认验证数据表模型
	'RBAC_ROLE_TABLE'     => C('DB_PREFIX').'role',//角色表
	'RBAC_USER_TABLE'     => C('DB_PREFIX').'role_user',//角色用户中间表
	//'RBAC_ACCESS_TABLE'   => C('DB_PREFIX').'access',//权限表
	//'RBAC_NODE_TABLE'     => C('DB_PREFIX').'node',//节点表
	//'DEFAULT_V_LAYER'       =>  'Template', // 设置默认的视图层名称
	'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
	'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
	'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true
		
	//图片上传允许的存储目录
    'imageSavePath' => array (
        'uploads/articleimages/upload'
    )
);