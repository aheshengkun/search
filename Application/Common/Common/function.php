<?php
/**
$CK = A('CheckInput');
$id = $CK->in($Cname,$name,$CheckType,$default,$LengMin,$LengMax);

*in方法使用说明
*@param $CnName  字符串,中文名,出错时提示用
*@param $name 字符串,传入参数,可用 get.id或post.id,put.id等，  参考thinkphp的I方法
*@param $CheckType 字符串,过滤方法,
*@param $default(可选) 字符串,默认值
*@param $LengMin(可选) 数字,最短长度,为0时不限制
*@param $LengMax(可选) 数字,最大长度,为0时不限制

---$CheckType可用值:
intval 0或正整数
date   日期 如:2014-05-02
time   时间 如:16:25:00
datetime 长时间 如:2014-05-02 16:25:00
password 密码 自动md5后，取前28位字符
bool   是/否 除输入为0外, 其它都返回 1;
float  小数 如:1253.123, 不限制小数点多少位
float(8,2) 小数,整数8位,小数2位,8和2可改
email 邮箱格式,（后期加防注入)
saveimage 上传图片,最短长度不为0，即为必须上传，现在每个页面仅支持上传一个图片
enstr  仅为字母,
numstr 仅为数字,
ennumstr  字母,数字,下划线,(防注入)
cnennumstr  中文,英文,数字,下划线,(即不允许有特殊字符)(防注入)
string 任意字符串,后期加入防sql注入,防xss,防javascript,尽量不要使用这种方式过滤
htcontent 内容型,后台在线内容编辑器专用,前台内容编辑器也不能使用,其它地方尽量不要使用,防sql注入,不防javascript

例：(取多个值时，只A一次即可)
$CK  = A('CheckInput');
$id  = $CK->in('id变量','get.id','intval','',0,3);
$str = $CK->in('密码','get.str','password','',1);
*/
function CheckInputFunc2($CnName, $name, $CheckType, $default = NULL, $LengMin = 0, $LengMax = 0) {
	$res ['ok'] = true;
	$res ['error'] = '';
	$res ['data'] = NULL;
	if (strpos ( $name, '.' )) { // 指定参数来源
		list ( $method, $name ) = explode ( '.', $name, 2 );
	} else { // 默认为自动判断
		$method = 'param';
	}
	switch (strtolower ( $method )) {
		case 'get' :
			$input = & $_GET;
			break;
		case 'post' :
			$input = & $_POST;
			break;
		case 'put' :
			parse_str ( file_get_contents ( 'php://input' ), $input );
			break;
		case 'param' :
			switch ($_SERVER ['REQUEST_METHOD']) {
				case 'POST' :
					$input = $_POST;
					break;
				case 'PUT' :
					parse_str ( file_get_contents ( 'php://input' ), $input );
					break;
				default :
					$input = $_GET;
			}
			break;
		case 'path' :
			$input = array ();
			if (! empty ( $_SERVER ['PATH_INFO'] )) {
				$depr = C ( 'URL_PATHINFO_DEPR' );
				$input = explode ( $depr, trim ( $_SERVER ['PATH_INFO'], $depr ) );
			}
			break;
		case 'request' :
			$input = & $_REQUEST;
			break;
		case 'session' :
			$input = & $_SESSION;
			break;
		case 'cookie' :
			$input = & $_COOKIE;
			break;
		case 'server' :
			$input = & $_SERVER;
			break;
		case 'globals' :
			$input = & $GLOBALS;
			break;
		case 'data' :
			$input = & $datas;
			break;
		case 'file' :
			$input = & $_FILES;
			break;
		default :
			return NULL;
	}
	if (empty ( $name )) { // 获取全部变量
		$res ['ok'] = false;
		$res ['error'] = '方法传入参数错误！';
		return $res;
	} elseif (isset ( $input [$name] )) { // 取值操作
		$data = $input [$name];
	} else { // 变量默认值
		$data = isset ( $default ) ? $default : NULL;
	}
	
	if ($LengMin != 0) {
		if ($data == '' || $data == NULL) {
			$res ['ok'] = false;
			$res ['error'] = "请填写【 $CnName 】！";
			return $res;
		}
		if (mb_strlen ( $data ,'utf8' ) < $LengMin) {
			$res ['ok'] = false;
			$res ['error'] = "【 $CnName 】太短,请重输！";
			return $res;
		}
	} else {
		if ($data == '' || $data == NULL) {
			$res ['ok'] = true;
			$res ['data'] = $data;
			return $res;
		}
	}
	if ($LengMax != 0) {
		if (mb_strlen ( $data ,'utf8') > $LengMax) {
			$res ['ok'] = false;
			$res ['error'] = "【 $CnName 】太长,请重输！";
			return $res;
		}
	}
	
	// $res['data']=$data;
	// return $res;
	
	$CKT = new CheckInputClass2 ();
	$CKT->CnName = $CnName;
	$CKT->CheckType = $CheckType;
	$CKT->data = $data;
	$CKT->LengMin = $LengMin;
	
	return $CKT->check ();
}

class CheckInputClass2 {
	var $CnName; // 中文名
	var $CheckType; // 过滤类型
	var $data; // 要过滤的变量
	var $res; // 反回的数组
	var $LengMin;
	var $postfilter = "<[^>]*?=[^>]*?&#[^>]*?>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b[^>]*?>|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
	var $htcontentfilter = "<[^>]*?=[^>]*?&#[^>]*?>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b[^>]*?>|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.+?\\*\\/|<\\s*scriiipt\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
	function __construct() {
	}
	function check() {
		switch ($this->CheckType) {
			case 'intval' :
				$this->_intval ();
				break;
			case 'date' :
				$this->_date ();
				break;
			case 'time' :
				$this->_time ();
				break;
			case 'datetime' :
				$this->_datetime ();
				break;
			case 'password' :
				$this->_password ();
				break;
			case 'string' :
				$this->_string ();
				break;
			case 'enstr' :
				$this->_enstr ();
				break;
			case 'numstr' :
				$this->_numstr ();
				break;
			case 'ennumstr' :
				$this->_ennumstr ();
				break;
			case 'cnennumstr' :
				$this->_cnennumstr ();
				break;
			case 'htcontent' :
				$this->_htcontent ();
				break;
			case 'float' :
				$this->_float ();
				break;
			case 'bool' :
				$this->_bool ();
				break;
			case 'email' :
				$this->_email ();
				break;
			case 'saveimage' :
				$this->_saveimage ();
				break;
			case 'uploadFile' :
				$this->_uploadFile();
				break;
			case 'shenfenzheng':
				$this->_shenfenzheng();
				break;
			default :
				// 处理特殊值 如 float(11,2)
				if ((strpos ( $this->CheckType, 'float' )) !== false) {
					$this->_floatex ();
				} elseif (false) {
				} else {
					$this->res ['ok'] = false;
					$this->res ['error'] = '函数过滤方法填写错误';
				}
		}
		return $this->res;
	}
	private function _saveimage() {
		if (C ( 'CheckInputsaveimage' ) == NULL) {
			// echo '===================';
			$upload = new \Think\Upload (); // 实例化上传类
			$upload->autoSub = true; // 是否使用子目录保存上传文件
			$upload->subType = 'date'; // 子目录创建方式，默认为hash，可以设置为hash或者date
			$upload->dateFormat = 'd'; // 子目录方式为date的时候指定日期格式
			$upload->maxSize = 1887437; // 设置附件上传大小
			$upload->exts = array (
					'jpg',
					'gif',
					'png',
					'jpeg' 
			); // 设置附件上传类型
			$upload->rootPath = 'uploads'; // 设置附件上传根目录
			$upload->savePath = '/images/' . date ( 'Y-m', time () ) . '/'; // 设置附件上传目录
			$upload->replace = true;
			// $upload->mimes = array('image/jpeg','image/jpg','image/gif','image/png'); //允许上传的文件类型（留空为不限制）
			// $upload->callback=true;
			$info = $upload->upload ();
			if (! $info) { // 上传错误提示错误信息
				$error = $upload->getError ();
				if (strpos ( $error, '没有文件被上传' ) !== false) {
					// echo '没有文件上传';
				} else {
					$this->res ['ok'] = false;
					$this->res ['error'] = '【 ' . $this->CnName . ' 】' . $error;
					return 0;
				}
			}
			C ( 'CheckInputsaveimage', $info );
		} else {
			$info = C ( 'CheckInputsaveimage' );
		}
		
		// dump($info);
		// dump($this->data);
		
		$savefile = $info [$this->data] ["savepath"] . $info [$this->data] ["savename"];
		
		if ($savefile == '' || $savefile == null) {
			$savefile = '';
			if ($this->LengMin > 0) {
				$this->res ['ok'] = false;
				$this->res ['error'] = '【 ' . $this->CnName . ' 】必须上传,请重输！';
				return 0;
			}
		} else {
			// $savefile='/uploads'.$savefile;
			$savefile = 'uploads' . $savefile;
		}
		
		$this->res ['ok'] = true;
		$this->res ['data'] = $savefile;
		
		// die();
		// 保存表单数据 包括附件数据
		// return $savePath.$info[0]['savename']; // 保存上传的照片根据需要自行组装
	}
	
	/*
	 * 上传文件($_File)
	 */
	private function _uploadFile(){
		$upload = new \Think\Upload (); // 实例化上传类
		$upload->autoSub = true; // 是否使用子目录保存上传文件
		$upload->subType = 'date'; // 子目录创建方式，默认为hash，可以设置为hash或者date
		$upload->dateFormat = 'd'; // 子目录方式为date的时候指定日期格式
		$upload->maxSize = 1887437; // 设置附件上传大小
		$upload->exts = array (
				'jpg',
				'gif',
				'png',
				'jpeg'
		); // 设置附件上传类型
		$upload->rootPath = 'uploads'; // 设置附件上传根目录
		$upload->savePath = '/images/' . date ( 'Y-m', time () ) . '/'; // 设置附件上传目录
		$upload->replace = true;
		$info = $upload->upload ();
		if (! $info) { // 上传错误提示错误信息
			$error = $upload->getError ();
			if (strpos ( $error, '没有文件被上传' ) !== false) {
				// echo '没有文件上传';
			} else {
				$this->res ['ok'] = false;
				$this->res ['error'] = '【 ' . $this->CnName . ' 】' . $error;
				return 0;
			}
		}
		
		$savefile = $info [key($info)] ["savepath"] . $info [key($info)] ["savename"];
		
		if ($savefile == '' || $savefile == null) {
			$savefile = '';
			if ($this->LengMin > 0) {
				$this->res ['ok'] = false;
				$this->res ['error'] = '【 ' . $this->CnName . ' 】必须上传,请重输！';
				return 0;
			}
		} else {
			$savefile = 'uploads' . $savefile;
		}
		
		$this->res ['ok'] = true;
		$this->res ['data'] = $savefile;
		
	}
	
	private function _htcontent() {
		// if (preg_match("/".$this->htcontentfilter."/is",$this->data)==1){
		// $this->res['ok']=false;
		// $this->res['error']='【 '.$this->CnName.' 】包含非法字符,请重输！';
		// return 0;
		// }
		$this->res ['ok'] = true;
		$this->res ['data'] = ($this->data);
	}
	private function _cnennumstr() {
		if (preg_match ( "/" . $this->postfilter . "/is", $this->data ) == 1) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】包含非法字符,请重输！';
			return 0;
		}
		// if(!preg_match("/^[0-9a-zA-Z_]{1,}$/",$this->data)){
		// if(!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$this->data)){
		if (! preg_match ( "/^[\x80-\xff_a-zA-Z0-9]+$/", $this->data )) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】不能包含特殊字符,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = ($this->data);
	}
	private function _string() {
		if (preg_match ( "/" . $this->postfilter . "/is", $this->data ) == 1) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】包含非法字符,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = ($this->data);
	}
	private function _ennumstr() {
		if (preg_match ( "/" . $this->postfilter . "/is", $this->data ) == 1) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】包含非法字符,请重输！';
			return 0;
		}
		if (! preg_match ( "/^[0-9a-zA-Z_]{1,}$/", $this->data )) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】只能输入数字,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	private function _numstr() {
		if (! preg_match ( "/^[-]{0,1}[0-9]{1,}$/", $this->data )) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】只能输入整数,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	private function _enstr() {
		if (preg_match ( "/" . $this->htcontentfilter . "/is", $this->data ) == 1) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】包含非法字符,请重输！';
			return 0;
		}
		if (! preg_match ( "/^[a-zA-Z]{1,}$/", $this->data )) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】只能输入英文字母,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	private function _email() {
		if (preg_match ( "/" . $this->postfilter . "/is", $this->data ) == 1) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】包含非法字符,请重输！';
			return 0;
		}
		if (! preg_match ( "/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$/", $this->data )) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】格式错误,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	private function _floatex() {
		$pa = '/float\((.*),(.*)\)/';
		preg_match_all ( $pa, $this->CheckType, $ma );
		// dump($ma[1][0]);
		// dump($ma[2][0]);
		if (! preg_match ( "/^[-]{0,1}\d{0," . $ma [1] [0] . "}\.{0,1}(\d{0," . $ma [2] [0] . "})?$/", $this->data )) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】只能输入整数或小数,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	private function _float() {
		try {
			$str = ( double ) ($this->data);
		} catch ( Exception $e ) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】只能输入整数或小数,请重输！';
			return 0;
		}
		if (( string ) ($str) != $this->data) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】只能输入整数或小数,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	private function _bool() {
		if ($this->data == 0) {
			$this->res ['data'] = 0;
		} else {
			$this->res ['data'] = 1;
		}
		$this->res ['ok'] = true;
	}
	private function _password() {
		$this->res ['ok'] = true;
		$this->res ['data'] = substr ( md5 ( $this->data ), 0, 28 );
	}
	private function _intval() {
		if (! preg_match ( "/^[-]{0,1}[0-9]{1,}$/", $this->data )) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】只能输入整数,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
		/*
		 * if( strval($this->data) == strval(intval($this->data)) ){ $this->res['ok']=true; $this->res['data']=$this->data; }else{ $this->res['ok']=false; $this->res['error']='【 '.$this->CnName.' 】只能输入数字,请重输！'; }
		 */
	}
	private function _date() {
		if (! preg_match ( '/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $this->data )) {
			if (! preg_match ( '/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $this->data )) {
				$this->res ['ok'] = false;
				$this->res ['error'] = '【 ' . $this->CnName . ' 】格式错误,请重输！';
				return 0;
			}
		}
		if (strtotime ( $this->data ) === false) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】格式错误,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	private function _time() {
		if (! preg_match ( '/^[0-2]{0,1}[0-9]{1}[:]{1}[0-5]{0,1}[0-9]{1}[:]{1}[0-5]{0,1}[0-9]{1}$/', $this->data )) {
			if (! preg_match ( '/^[0-2]{0,1}[0-9]{1}[:]{1}[0-5]{0,1}[0-9]{1}$/', $this->data )) {
				$this->res ['ok'] = false;
				$this->res ['error'] = '【 ' . $this->CnName . ' 】格式错误,请重输！';
				return 0;
			}
		}
		if (strtotime ( $str_tmp ) === false) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】格式错误,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	private function _datetime() {
		if (! preg_match ( '/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $this->data )) {
			if (! preg_match ( '/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}$/', $this->data )) {
				$this->res ['ok'] = false;
				$this->res ['error'] = '【 ' . $this->CnName . ' 】格式错误,请重输！';
				return 0;
			}
		}
		if (strtotime ( $this->data ) === false) {
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】格式错误,请重输！';
			return 0;
		}
		$this->res ['ok'] = true;
		$this->res ['data'] = $this->data;
	}
	
	private function _shenfenzheng(){
		if(ck_shengfenzheng($this->data)){
			$this->res ['ok'] = true;
			$this->res ['data'] = $this->data;			
		}else{
			$this->res ['ok'] = false;
			$this->res ['error'] = '【 ' . $this->CnName . ' 】格式错误,请重输！';
			return 0;
		}
	}
}




/**
 * 检查值是否是序列化。
 *
 * 如果$data不是字符串，返回值永远是错误的
 * 序列化的数据始终是一个字符串。
 *
 * @since 1.0.0
 *
 * @param mixed $data 检查是否序列号的值
 * @param bool $strict Optional. 是否严格检查字符串最后字符。默认True
 * @return bool 如果数值被序列号返回True,否则返回False.
 */
function is_serialized( $data, $strict = true ) {
	// if it isn't a string, it isn't serialized
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
	if ( 'N;' == $data ) {
		return true;
	}
	if ( mb_strlen( $data ,'utf8') < 4 ) {
		return false;
	}
	if ( ':' !== $data[1] ) {
		return false;
	}
	if ( $strict ) {
		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		// Either ; or } must exist.
		if ( false === $semicolon && false === $brace )
			return false;
		// But neither must be in the first X characters.
		if ( false !== $semicolon && $semicolon < 3 )
			return false;
		if ( false !== $brace && $brace < 4 )
			return false;
	}
	$token = $data[0];
	switch ( $token ) {
		case 's' :
			if ( $strict ) {
				if ( '"' !== substr( $data, -2, 1 ) ) {
					return false;
				}
			} elseif ( false === strpos( $data, '"' ) ) {
				return false;
			}
			// or else fall through
		case 'a' :
		case 'O' :
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}

/**
 * 序列化数据，如果需要的话。
 *
 * @since 1.0.0
 *
 * @param mixed $data 要序列号的数据
 * @return mixed 序列化后的数据
 */
function maybe_serialize( $data ) {
	if ( is_array( $data ) || is_object( $data ) )
		return serialize( $data );

	// Double serialization is required for backward compatibility.
	if ( is_serialized( $data, false ) )
		return serialize( $data );

	return $data;
}

/**
 * Unserialize 将已序列化的变量进行操作，将其转换回 PHP 的值.
 * 
 * @since 1.0.0
 *
 * @param string $original 已序列化的数据
 * @return mixed 返回php的值.
 */
function maybe_unserialize( $original ) {
	if ( is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
		return @unserialize( $original );
	return $original;
}

/**
 * 将值变成非负整数
 *
 * @since 1.0
 *
 * @param mixed $maybeint Data 要转换的数据
 * @return int 一个非负整数
 */
function absint( $maybeint ) {
	return abs( intval( $maybeint ) );
}

/**
 * 基于用户ID更新用户字段信息
 *
 * 使用$ prev_value参数使用相同的密钥和用户ID的元信息来区分
 *
 * 如果元字段的用户不存在，它将被添加。
 *
 * @since 1.0.0
 * @uses update_metadata
 *
 * @param int $user_id User ID.
 * @param string $meta_key (必须)  meta_key对应zb_customermeta 表中meta_value 被更新.
 * @param mixed $meta_value (必须) 该meta_key的新的期望值，它必须和现有的值不同。数组和对象将被自动序列化。请注意，使用对象可能会导致这个错误弹出。
 * @return 更新成功返回TRUE，更新失败返回FALSE
 */
function update_user_meta($user_id, $meta_key, $meta_value) {
	        return update_metadata('user', $user_id, $meta_key, $meta_value);
}

/**
 * 更新为指定对象的元数据
 * @author chan [234315301@qq.com]
 *
 * @since 1.0.0
 *
 * @param string $meta_type 对象的元数据类型 (e.g., comment, post, or user)
 * @param int $object_id 对象的ID
 * @param string $meta_key 关键对象
 * @param mixed $meta_value 元数据值。如果非标量必须是可序列化的。
 * @return int|bool 更新成功返回True 失败返回false
 */
function update_metadata($meta_type, $object_id, $meta_key, $meta_value) {
	if ( !$meta_type || !$meta_key )
		return false;
	
	if ( !$object_id = absint($object_id) )
		return false;
	
	$meta = M('customermeta');
	switch ($meta_type){
		case user:
			$metadate = $meta->where("customer_id = '".$object_id."' and meta_key = '".$meta_key."'")->select();
			break;
	}
	if($metadate){
		$data['meta_key']    = $meta_key;
		$data['meta_value']  = maybe_serialize( $meta_value );
		$result = $meta->where('customer_id = '.$object_id)->data($data)->save(); // 根据条件保存修改的数据
	}else{
		$data['customer_id']    = $object_id;
		$data['meta_key']    = $meta_key;
		$data['meta_value']  = maybe_serialize( $meta_value );
		$result = $meta->data($data)->add();
	}
	
	if($result){
		return true;
	}else{
		return false;
	}
	
}

/**
 * 获取用户 meta field 的值。
 *
 * @since 1.0.0
 * @uses get_metadata()
 *
 * @param int $user_id 用户 ID.
 * @param string $key Optional. 根据关键词来检索。默认情况下，所有的键返回数据
 * @param bool $single 是否返回单个值.
 * @return mixed 如果$single是false 则返回数组。如果$single是true 则返回单个值
 * 
 */
function get_user_meta($user_id, $key = '', $single = false) {
	return get_metadata('user', $user_id, $key, $single);
}

/**
 * 获取指定对象的元数据。
 * @author chan [234315301@qq.com]
 * @since 1.0.0
 *
 * @param string $meta_type 获取对象的类型 (e.g., comment, post, or user)
 * @param int $object_id ID 对象的id
 * @param string $meta_key Optional. 根据关键词获取值，如果没有指定者获取所有数据
 * @param bool $single Optional, default is false. 如果是true则返回第一个meta_key值。 没有指定meta_key则此参数无作用
 * @return string|array Single metadata value, or array of values
 */
function get_metadata($meta_type, $object_id, $meta_key = '', $single = false) {
	if ( !$meta_type )
		return false;

	if ( !$object_id = absint($object_id) )
		return false;
	
	$meta = M('customermeta');
	
	switch ($meta_type){
		case user:
			if($meta_key){
				$metadate = $meta->where("customer_id = '".$object_id."' and meta_key = '".$meta_key."'")->select();
			}else{
				$metadate = $meta->where("customer_id = '".$object_id."'")->select();
			}
			break;
	}
	
	
	if ( isset($metadate) ) {
		if ( $single )
			return maybe_unserialize( $metadate[0]['meta_value'] );
		else
			return array_map('maybe_unserialize', $metadate);
	}

	if ($single)
		return '';
	else
		return array();
}

/**
 * 地区联动
 *
 * @since 1.0.0
 *
 * @param int $areaid 区域对应的id
 * @return string 返回省-城乡-地区
 */
function area($areaid){
	$M = M();
	$prefix = C('DB_PREFIX');
	$info = $M->Table("{$prefix}area as p1")->
	join("{$prefix}area as p2 on p1.pid=p2.id")->
	join("{$prefix}area as p3 on p2.pid=p3.id")->
	field('p3.name as provincename,p2.name as cityname,p1.name as ereaname')->
	where("p1.id='$areaid'")->find();
	return implode(' ',$info);

}

/**
 * 数字转换
 *
 * @since 1.0.0
 *
 * @param  float $fnum 
 * @return float 返回转换数字
 */
function GetNum($fnum)
{
	$nums  = array("０","１","２","３","４","５","６","７","８","９");
	$fnums = array("0","1","2","3","4","5","6","7","8","9");
	$fnum  = str_replace($nums,$fnums,$fnum);
	$fnum  = ereg_replace("[^0-9\.-]",'',$fnum);
	if($fnum==''){
		$fnum=0;
	}
	return $fnum;
}

/**
 * auth: zhangriheng,格式化打印数组,仅用于开发调试阶段
 */
function p($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

 
/**
 * auth: zhangriheng
 * Utf-8、gb2312都支持的汉字截取函数 
 * cut_str(字符串, 截取长度, 开始长度, 编码); 
 * 编码默认为 utf-8 
 * 开始长度默认为 0
 */
function str_cut($string, $sublen, $start = 0, $code = 'UTF-8'){
	if($code == 'UTF-8') {
		$pa ="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		preg_match_all($pa, $string, $t_string);
		if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
		return join('', array_slice($t_string[0], $start, $sublen));
	}else{
		$start = $start*2;
		$sublen = $sublen*2;
		$strlen = strlen($string);
		$tmpstr = '';
		for($i=0; $i<$strlen; $i++) {
			if($i>=$start && $i<($start+$sublen)) {
				if(ord(substr($string, $i, 1))>129) {
					$tmpstr.= substr($string, $i, 2);
				}else{
					$tmpstr.= substr($string, $i, 1);
				}
			}
			if(ord(substr($string, $i, 1))>129) $i++;
		}
		if(strlen($tmpstr)<$strlen ) $tmpstr.= "...";
		return $tmpstr;
	}
}
/**
*与str_cut功能相同（备用方法）  auth: JZ
* {$str|msubstr=$start,$length,$charset,$suffix}//模板调用
* 截取一定长度的字符串，确保截取后字符串不出乱码
* @param    string  $str     	想要截取的字符串
* @param    int     $start   	想要截取的字符串起始位置
* @param    int     $length   	想要截取的字符串最大长度
* @param    string  $charset   	字符编码格式
* @param    string  $charset   	字符串超过最大长度是否用‘···’代替
*
**/
/*
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr")){
        $slice = mb_substr($str, $start, $length, $charset);
    }
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312']  = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']     = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']    = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    $fix='';
    if(strlen($slice) < strlen($str)){
        $fix='...';
    }
    return $suffix ? $slice.$fix : $slice;
}
*/


/**
 * 计算利息收益
 * @author chan [234315301@qq.com]
 * @since 1.0.0
 *
 * @param  float $money 金额
 * @param  float $apr 年利率(百分比)
 * @param  float $time_limit 期限 (月份)
 * @param  float $isday 是否天标 (0否1是) by hcf 20141120
 * @param  float $style 标的还款类型 (1到期还本付息 2等额本息，按月还款 3按月付息，到期还本) by lhq 20150104
 * @return float 返回利息收益	 
 */
/* function profit($money,$apr,$time_limit,$style,$isday='0'){
	if($isday=='0') return ($money*$apr*$time_limit)/(12*100);
	else return ($money*$apr*$time_limit)/(365*100);
} */

function profit($money,$apr,$time_limit,$style,$isday='0'){
	if($isday=='0'){
		$i=$apr * 0.01 /12;//月利率
		if($style=='1'){
			return ($money*$apr*$time_limit)/(12*100);
		}elseif($style=='2'){
			$b =$money * $i * pow((1+$i),$time_limit)/(pow((1+$i),$time_limit)-1);//月均还款
			$Y =$time_limit * $b - $money;//总收益
			return $Y;
		}elseif($style=='3'){
			$Y =$time_limit * $i * $money;//总收益
			return $Y;
		}else{
			return ($money*$apr*$time_limit)/(12*100);
		}				
	}else{
		return ($money*$apr*$time_limit)/(365*100);
	}
}


/**
 * 计算不同还款方式的每月利息收益 或总收益
 * @author 
 * @param  float $money 金额
 * @param  float $apr 年利率(百分比)
 * @param  float $time_limit 期限 (月份)
 * @param  float $isday 是否天标 (0否1是)
 * @param  float $style 标的还款类型 (1到期还本付息 2等额本息，按月还款 3按月付息，到期还本) 
 * @return float 返回利息收益	 
 */
function profitByMonth($money,$apr,$time_limit,$style='1',$isday='0'){
	if($isday=='0'){
		$i=$apr * 0.01 /12;//月利率
		if($style=='1'){
			return ($money*$apr*$time_limit)/(12*100);
		}elseif($style=='2'){
			$b =$money * $i * pow((1+$i),$time_limit)/(pow((1+$i),$time_limit)-1);//月均还款
			// $Y =$time_limit * $b - $money;//总收益
			return $b;
		}elseif($style=='3'){
			$Y = $i * $money;//月还款(利息)
			return $Y;
		}else{
			return ($money*$apr*$time_limit)/(12*100);
		}				
	}else{
		return ($money*$apr*$time_limit)/(365*100);
	}
}

/**
 * 保留两位小数不四舍五入
 * @author chan [234315301@qq.com]
 * @param  float $num 保留两位小数的数据
 * @return float 返回浮点数
 */
function two_decimal_places( $num){
	//return sprintf("%.2f",substr(sprintf("%.3f", '2.333333'), 0, -2));
	$p = stripos($num, '.');
	if($p){
		// return substr($num,0,$p+3);
		return substr($num,0,$p+3);
	}else{
		return $num;
	}
}

/**
 * 以万为单位生成数据 
 * @author chan [234315301@qq.com]
 * @param  float  要格式化数字
 * @return float 返回格式化后数字
 */
function formatMoney($money){
	if($money >= 10000){
		$money = sprintf("%.0f", $money/10000);
		return number_format($money,0,'.',',').'万';
	}else{
		return number_format($money,0,'.',',');
	}
}


/**
 * 获取当前页面完整URL
 * @author chan [234315301@qq.com]
 * @since 1.0.0
 * @return string 当前域名
 */
function get_current_url(){
	return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

/**
 * 按指定路径生成目录
 * @param    string     $path    路径
 */
function mkDirs($path){
	$adir = explode('/',$path);
	$dirlist = '';
	$rootdir = array_shift($adir);
	if(($rootdir!='.'||$rootdir!='..')&&!file_exists($rootdir)){
		@mkdir($rootdir);
	}
	foreach($adir as $key=>$val){
		if($val!='.'&&$val!='..'){
			$dirlist .= "/".$val;
			$dirpath = $rootdir.$dirlist;
			if(!file_exists($dirpath)){
				@mkdir($dirpath);
				@chmod($dirpath,0755);
			}
		}
	}
}

/**
 * 随机生成字符串
 * @author chan [234315301@qq.com]
 * @param string $length 多少位随机数
 */
function getRandStr($length) {  
	$str = 'abcdefghijklmnopqrstuvwxyz'; 
	$randString = ''; 
	$len = strlen($str)-1; 
	for($i = 0;$i < $length;$i ++){ 
		$num = mt_rand(0, $len); 
		$randString .= $str[$num]; 
	} 
	return $randString ;
}

/**
 * 获取用户头像
 * @param  int $id  用户id
 */
function get_avatar($id){
	$objAvatar = new \Org\Util\Avatar();
	$avatar    = $objAvatar->avatar_show($id,'big','true');
	return $avatar;
}

/**
 * 验证身份证号
 * @author chan [234315301@qq.com]
 * @param $vStr string 身份证号码
 * @return bool  返回状态
 */
function ck_shengfenzheng($vStr)
{
	$vCity = array(//各省份身份证号前两位
			'11','12','13','14','15','21','22',
			'23','31','32','33','34','35','36',
			'37','41','42','43','44','45','46',
			'50','51','52','53','54','61','62',
			'63','64','65','71','81','82','91'
	);
	
	if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;

	if (!in_array(substr($vStr, 0, 2), $vCity)) return false;

	$vStr = preg_replace('/[xX]$/i', 'a', $vStr);
	$vLength = strlen($vStr);

	if ($vLength == 18)
	{
		$vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
	} else {
		$vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
	}

	if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
	if ($vLength == 18)
	{
		$vSum = 0;

		for ($i = 17 ; $i >= 0 ; $i--)
		{
			$vSubStr = substr($vStr, 17 - $i, 1);
			$vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
		}
		
		if($vSum % 11 != 1) return false;
	}
	return true;
}

/**
 * 判断图片是否存在
 * @author chan [234315301@qq.com]
 * @param $url string 图片绝对链接
 * @return bool  返回状态
 */
function issetimg($url){	
	if(@fopen($url,'r')){
		return true;
	}else{
		return false;
	}
}

/**
 * 某一时间，多少个月后
 * @author chan [234315301@qq.com]
 * $param $time string 某一时间戳
 * $param $month  int 多少个月后
 * $param $isday  是否按天计算 0否1是
 * @return string  时间戳
 */
function nextTime($time,$month,$isday='0'){
	$s = date('s',$time);
	$i = date('i',$time);
	$h = date('H',$time);
	$d = $isday=='1'?(date('d',$time)+$month):date('d',$time);
	$m = $isday=='0'?(date('m',$time)+$month):date('m',$time);
	$Y = date('Y',$time);
	//hour	可选。规定小时。
	//minute	可选。规定分钟。
	//second	可选。规定秒。
	//month	可选。规定用数字表示的月。
	//day	可选。规定天。
	//year	可选。规定年。在某些系统上，合法值介于 1901 - 2038 之间。不过在 PHP 5 中已经不存在这个限制了。
	return mktime($h,$i,$s,$m,$d,$Y);
}


/** 获取指定日期当天的开始时间与结束时间 返回开始和结束时间戳格式 2015-1-16 13:13:05
 * $date 传入当前时间的时间戳
 * $type 要获取的时间类型，-1前一天的，0当前的，
*/

// //php获取今日开始时间戳和结束时间戳
// $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
// $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

// //php获取昨日起始时间戳和结束时间戳

// $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
// $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;


function getDayRange($date,$type='0'){
	if($type == '0'){//当天的
		$ret['sdate']=strtotime(date('Y-m-d 00:00:00',$date));
    	$ret['edate']=strtotime(date('Y-m-d 23:59:59',$date));
	}else{//前一天的
		$ret['sdate']=strtotime(date('Y-m-d 00:00:00',$date-86400));
    	$ret['edate']=strtotime(date('Y-m-d 23:59:59',$date-86400));
	}
	return $date;
}

/** 获取指定日期所在星期的开始时间与结束时间 2015-1-16 13:13:05
 * $date 传入当前时间的时间戳
 * $type 要获取的时间类型，-1前一周的，0当前的，
*/
function getWeekRange($date,$type='0'){
	$ret['sdate']=mktime(0,0,0,date('m',$date),date('d',$date)-date('w',$date)+1,date('Y',$date));
	$ret['edate']=mktime(23,59,59,date('m',$date),date('d',$date)-date('w',$date)+7,date('Y',$date));
	return $ret;
}

/** 获取指定日期所在月的开始日期与结束日期 返回时间戳格式 2015-1-16 13:13:05
 * $date 传入当前时间的时间戳
 * $type 要获取的时间类型，-1前一月的，0当前的，
*/
function getMonthRange($date,$type='0'){
	$ret=array();
	if($type == '0'){//当前月
		$mdays=date('t',$date);//传入时间戳所在月共多少天
		$ret['sdate']=strtotime(date('Y-m-1 00:00:00',$date));
		$ret['edate']=strtotime(date('Y-m-'.$mdays.' 23:59:59',$date));
	}else{//前一月
		$prem = date('m',$date)-1;
		$m = date('Y-m-d', mktime(0,0,0,$prem,1,date('Y'))); //上个月的开始日期
		$t = date('t',strtotime($m)); //上个月共多少天
		$ret['sdate']=strtotime(date('Y-'.$prem.'-1 00:00:00',$date));
		$ret['edate']=strtotime(date('Y-'.$prem.'-'.$t.' 23:59:59',$date));
	}
    return $ret;
}



//二维数组去掉重复值
function array_unique_fb($array2D){
	foreach ($array2D as $v){
		$v=join(',',$v);  //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
		$temp[]=$v;
	}
	$temp=array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组
	foreach ($temp as $k => $v){
		$temp[$k]=explode(',',$v);   //再将拆开的数组重新组装
	}
	return $temp;
}


//二维数组排序
function multi_array_sort($multi_array,$sort_key,$sort='SORT_ASC'){  
    if(is_array($multi_array)){  
        foreach ($multi_array as $row_array){  
            if(is_array($row_array)){  
                $key_array[] = $row_array[$sort_key];  
            }else{  
                return -1;  
            }  
        }  
    }else{  
        return -1;  
    }  
    array_multisort($key_array,$sort,$multi_array);  
    return $multi_array;  
}

/**
 * 根据id 获取支付模块信息
 * @author chan [234315301@qq.com]
 * $param $id  支付模块ID
 * @return 相应支付模块信息
 */
function get_payments($id){
	$payment = M('Payment');
	$res = $payment->where("id = '{$id}'")->find();
	return $res;
}

/**
 * 发送短信方法
 * @author chan [234315301@qq.com]
 * @param $mun string 电话号码
 * @param $type string  recharge:充值 ;repayment:还款;cashOK:提现成功 ;tender:投标 ;cash:申请提现 ;checkPhone:手机验证;
 * @param $txt  string  recharge->金钱额度;repayment->NULL;cashOK->NULL;tender->投资id;cash->$time、$txt;checkPhone->验证码;
 * @param $time string  时间
 * @return bool 状态
 * 例如：(发送充值100元信息)send_messages('185944565','recharge','100')
 * 注：以下格式不能修改！！！
 */
function send_messages($mobile,$type,$txt = NULL,$time = NULL){
	switch ($type) {
		case 'recharge': //充值成功
			$p = "尊敬的用户：您充值￥".$txt."元已成功！";
			break;
		case 'repayment': //还款成功
			$p = '尊敬的用户：您收到一笔回款，请注意查收！';
			break;
		case 'cashOK': //提现成功
			$p = '尊敬的用户：您的提现申请已处理，请注意查收！';
			break;
		case 'tender': //投标成功(满标审核成功)
			$p = "尊敬的用户：您所投资的ID为".$txt."的标已满标审核成功！";
			break;
		case 'liutender': //流转标投标发送信息
			$p = "尊敬的用户：您所投-资的ID为".$txt."的标已成功！";
			break;				
		case 'cash': //申请提现
			$p = "尊敬的用户：您于".$time."申请提现".$txt."元。";
			break;
		case 'checkPhone': //验证手机
			$p = "尊敬的用户：您刚才申请的认证验证码为".$txt." 。";
			break;
		default:
			break;
	}
	
	import('Org.Util.SMSer');
	return zhaobao_SendSMS($mobile,$p);
}

/**
 * 加密(解密)字符串
* @param string $string 原文或者密文
* @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE 解密
* @param string $key 密钥
* @param int    $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
* @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
*
* @example
*
*  $a = encryptcode('abc', 'ENCODE', 'key');
*  $b = encryptcode($a, 'DECODE', 'key');  
*
*  $a = encryptcode('abc', 'ENCODE', 'key', 3600);
*  $b = encryptcode('abc', 'DECODE', 'key'); // 在一个小时内解密，否则 $b 为空
*/
function encryptcode($string, $operation = 'DECODE', $key = '', $expiry = 3600) {

	$ckey_length = 4;
	// 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	// 当此值为 0 时，则不产生随机密钥

	$key = md5($key ? $key : 'xhcf360'); //这里可以填写默认key值
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a 		 = ($a + 1) % 256;
		$j 		 = ($j + $box[$a]) % 256;
		$tmp 	 = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

/**
 * 把数字转换成中文大写
 * @author chan [234315301@qq.com]
 * @param  string $money 要转换的数字
 * @return string 转换成功的字符串
 */
function chinese_capital($money) {
	static $cnums    = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"), $cnyunits = array("元","角","分"),$grees=array("拾","佰","仟","万","拾","佰","仟","亿");
	
	list($m1,$m2)    = explode(".",$money,2); //根据小数点截取得到数组，然后list用数组中的元素为一组变量赋值
	$m2              = array_filter(array($m2[1],$m2[0])); //把小数点后面数字分解成一个数 如：0.23 变成Array ( [0] => 3 [1] => 2 )
	$m1_split        = str_split($m1); //分割字符，每一个字符变成一个字符元素
	
	$integer 		 = digital_plus_unit($m1_split,$grees);
	
	/*把整合好的整数跟小数合并成一个数组。如:
	 * array(4) {
	 *	  [0] => string(1) "3"
	 *	  [1] => string(1) "2"
	 *	  [2] => string(17) "1拾万09佰8拾4"
	 *	  [3] => string(0) ""
	 *	}
	 */
	$ret          = array_merge($m2,array(implode("",$integer),""));//把整数部分加上单位
	$ret          = implode("",array_reverse(digital_plus_unit($ret,$cnyunits)));//小数部分加上单位，然后翻转回原来数字
	if(empty($m2)){$zheng = '整';}
	return str_replace(array_keys($cnums),$cnums,$ret).$zheng; //把数字按照$cnums 的key 对应，转换成中文大写
}
/**
 * chinese_capital 函数里面调用，给数字加上中文单位。（避免重复写代码）
 * @author chan [234315301@qq.com]
 * @param  array $digital 数字数组
 * @param  array $units 单位数组
 * @return array 加上单位的数字
 */
function digital_plus_unit($digital,$units) {
	$ul              = count($units); //统计$grees的个数	
	$reform_m1_split = array();
	foreach (array_reverse($digital) as $x) {  //array_reverse() 函数将原数组中的元素顺序翻转，创建新的数组并返回
		$l = count($reform_m1_split); //统计整数部分的个数
		if ($x != "0" || !($l % 4)){ //当前位数值不等于0  全部整数部分个数是4的整数倍
			$n = ($x == '0'?'':$x).($units[($l-1)%$ul]); //整数部分十位取$grees单位数之余;
		}else {
			$n = is_numeric($reform_m1_split[0][0])?$x:'';  //整数第一个不加单位
		}
		array_unshift($reform_m1_split,$n);  //合并数组
	}
	return $reform_m1_split;
}

/**
 * 积分操作
 * @author hcf 20141126
 * @param  string $customer_id 用户id
 * @param  string $nid 积分代码(参照credits_type表中的nid字段)
 * @param  string $value 默认为0,扩展功能。等于0,则按实际的value(积分)值计算,否则按传进来的值计算
 * @return array  $return 成功返回true,失败返回false
 */
function creditsHandle($customer_id,$nid,$value='0'){
	$userId=session('user_id');
	$userId=empty($userId) ? '0' : $userId;
	$time=time();
	$ip=get_client_ip();
	$return=array();
	$flag=false;

	$creditsLog=M('credits_log');
	$credits=M('credits');
	$creditsType=M('credits_type');

	$map['customer_id']=$customer_id;

	//查询用户积分表,获取该用户的积分信息
	$creditsResult=$credits->where(array('customer_id'=>$customer_id))->find();

	//查询积分类型表,获取积分类型信息
	$creditsTypeResult=$creditsType->where(array('nid'=>$nid))->find();

	//查询积分日志表,获取该用户的积分日志
	$map['type_id']=$creditsTypeResult['id'];
	$creditsLogResult=$creditsLog->where($map)->order('id desc')->select();
	$creditsLogNum=count($creditsLogResult);

	//是否有此积分类型(代码)
	if($creditsTypeResult){
		$awardTimes=$creditsTypeResult['award_times'];
		//奖励次数不能超过设定值
		if($awardTimes!='0'&&($awardTimes<=$creditsLogNum)){
			$flag=true;
		}
		
		$cycle=$creditsTypeResult['cycle'];
		//缓存次数
		if($cycle=='3'){
			$interval=$creditsTypeResult['interval'];//间隔分钟
			$s=$interval*60;
			$num=1;
			if(S('n'.$customer_id)){
				$n=S('n'.$customer_id);
				$num=$n+1;
			}
			S('n'.$customer_id,$num,$s);
		}
		
		//积分周期相关操作
		if($creditsResult&&$creditsLogResult){
			
			if($cycle=='1'&&$creditsLogNum>='1'){
				$return['message']=$creditsTypeResult['name'].'只能运行一次,不能再次参与积分活动!';
				$return['status']=false;
				return $return;
			}

			if($cycle=='2'){
				$t1=$creditsLogResult['0']['addtime'];//最近一次时间戳
				$d1=date('Y-m-d',$t1);
				$t2=strtotime('now');
				$d2=date('Y-m-d');
				
				//一天
				if($t1>$t2||($d1==$d2&&$flag)){
					$return['message']=$creditsTypeResult['name'].'只能每天运行'.$awardTimes.'次,请明天再尝试!';
					$return['status']=false;
					return $return;
				}
			}

			if($cycle=='3'){
				$t1=$creditsLogResult['0']['addtime'];//最近一次时间戳
				$t2=strtotime('+'.$interval.' minute',$t1);	
				//每隔M分钟执行N次
				if(($t2>$time&&$num>$awardTimes)&&$flag){
					$return['message']=$creditsTypeResult['name'].'每隔'.$interval.'分钟内运行'.$awardTimes.'次,请稍候再试!';
					$return['status']=false;
					return $return;
				}
			}

			if($cycle=='4'){
				if($flag){
					$return['message']=$creditsTypeResult['name'].'只能运行'.$awardTimes.'次,不能再次参与积分活动!';
					$return['status']=false;
					return $return;
				}
			}

		}
		//积分周期相关操作 end

		//用户积分表操作
		$data['op_user']=$userId;
		//积分表是否有该用户,执行不同的操作
		if($creditsResult){
			$data['value']=$value=='0' ? $creditsResult['value']+$creditsTypeResult['value'] : $creditsResult['value']+$value;
			$data['updatetime']=$time;
			$data['updateip']=$ip;
			$creditsStatus=$credits->where(array('customer_id'=>$customer_id))->save($data);
			if(false===$creditsStatus){
				$return['message']=$creditsTypeResult['name'].' 失败!更新用户积分出错.';
				$return['status']=false;
				return $return;
			}
		}else{
			$data['value']=$value=='0' ? $creditsTypeResult['value'] : $value;
			$data['customer_id']=$customer_id;
			$data['addtime']=$time;
			$data['addip']=$ip;
			$creditsStatus=$credits->add($data);
			if(!$creditsStatus){
				$return['message']=$creditsTypeResult['name'].'失败!添加用户积分出错.';
				$return['status']=false;
				return $return;
			}
		}
		//用户积分表操作 end

		//积分日志表操作
		$val=$value=='0' ? $creditsTypeResult['value'] : $value;
  		$log['customer_id']=$customer_id; 
  		$log['type_id']=$creditsTypeResult['id'];
  		$log['op']=$val>0 ? '1' : '2';
  		$log['score']=$creditsResult ? $creditsResult['value']+$val : $val;
  		$log['value']=$val;
  		$log['remark']=$creditsTypeResult['name'].'，变动 '.$val.' 积分.';
  		$log['op_user']=$userId;
  		$log['addtime']=$time;
  		$log['addip']=$ip;
  		$creditsTypeStatus=$creditsLog->add($log);

  		if(!$creditsTypeStatus){
  			$return['message']=$creditsTypeResult['name'].'失败!添加积分日志出错.';
			$return['status']=false;
			return $return;
  		}
  		//积分日志表操作 end
  		
	}else{
		$return['message']='没有 '.$creditsTypeResult['name'].' 积分名称,操作错误!';
		$return['status']=false;
		return $return;
	}
	//是否有此积分类型(代码) end if
	
	$return['message']=$creditsTypeResult['name'].'成功，变动 '.$val.' 积分';
	$return['status']=true;
	return $return;
}

/**
 * 显示用户积分等级信息
 * @author hcf 20141128
 * @param  string $customer_id 用户id
 * @return array  $return 成功返回true,失败返回false
 */
function creditsRankShow($customer_id){
	$return=array();
	if(empty($customer_id)){
		$return['message']='用户id不能为空!';
		$return['status']=false;
		return $return;
	}

	$credits=M('credits');
	$creditsRank=M('credits_rank');

	//获取用户积分
	$creditsInfo=$credits->lock(true)->alias('CR')->join("left join ".C('DB_PREFIX')."customer as CU ON CR.customer_id=CU.id")->where(array('customer_id'=>$customer_id))->field('name,customer_id,value')->find();
	if(!$creditsInfo){
		$return['message']='此用户不存在!';
		$return['status']=false;
		return $return;	
	}

	//组合查询条件
	$map['point1']=array('ELT',$creditsInfo['value']);
	$map['point2']=array('EGT',$creditsInfo['value']);
	$creditsRankInfo=$creditsRank->where($map)->find();
	if(!$creditsRankInfo){
		$return['message']='没有匹配的积分等级!';
		$return['status']=false;
		return $return;
	}

	//组装数组
	$creditsRankInfo['customer_id']=$customer_id;
	$creditsRankInfo['customer_name']=$creditsInfo['name'];
	$creditsRankInfo['value']=$creditsInfo['value'];
	$return['message']=$creditsRankInfo;
	$return['status']=true;
	return $return;
}

/**
 * 将二维数组转为按逗号分隔的字符串
 * author 
 * @param $arr 传入要处理的二维数组
 * @return $str 返回单引号引起来的每个字符且按逗号分隔
 */
function arr2str ($arr){
    foreach ($arr as $v){
        $v = join(",",$v); //可以用implode将一维数组转换为用逗号连接的字符串
        $temp[] = $v;
    }
    $t="";
    foreach($temp as $v){
        $t.="'".$v."'".",";
    }
    $t=substr($t,0,-1);
    return $t;
}

//html实体转换
function HtmlEncode($str) {
	return html_entity_decode($str,ENT_QUOTES,'UTF-8');
}


/** 2015-1-12 16:47:20
* $paymentNum--还款期数
* $balance--贷款本金
* $periodicPayment--每月还款额
* $paymentInterest--每月的利息
* $paymentPrincipal--每月本金减少额
* $monthlyInterest--月利率
* $result--此标的信息结果一维数组
* style--还款方式 1到期还本付息；2等额本息，按月还款；3按月付息，到期还本。
*/
function amortizationTable($paymentNum,$periodicPayment,$balance,$monthlyInterest,$result){
	if($result['style'] == 2){
		//round 四舍五入
		$paymentInterest = two_decimal_places($balance * $monthlyInterest);//每月的利息
		$paymentPrincipal = two_decimal_places($periodicPayment - $paymentInterest);//每月本金减少额
		$newBalance = two_decimal_places($balance - $paymentPrincipal);//下月计息的本金
		$ben = $periodicPayment - $paymentInterest; //还本金
		if($paymentNum == $result['time_limit']){
			//最后一期还款时
			$ben = $balance+$newBalance;
			$periodicPayment = $ben + $paymentInterest;
			$newBalance = 0;
		}
		// $newBalance = $balance - ($periodicPayment - $balance * $monthlyInterest)
	}else if($result['style'] == 3){
		//按月付息，到期还本
		// 每次（不包含最后一次）还款金额为：出借总额×月利率；
		// 最后一次还款金额为：出借总额×月利率 + 借款总额。
		// $paymentInterest = two_decimal_places($periodicPayment);//每月的利息
		$paymentInterest = $periodicPayment;//每月的利息
		$paymentPrincipal = 0;//每月本金减少额
		$newBalance = two_decimal_places($balance - $paymentPrincipal);//下月计息的本金
		$ben = $periodicPayment - $paymentInterest; //还本金
		if($paymentNum == $result['time_limit']){
			//最后一期还款时
			$ben = $balance;
			$periodicPayment = $balance + $periodicPayment;
			$newBalance = 0;
		}
	}

	//当前时间（年月日）
	$nowtime = date('Y-m-d',time());
	$nexttime = date("Y-m-d",nextTime($result['verify_time'],$paymentNum));//下次还款时间,publish改为verify_time
	$tablestr = '';

	$tablestr .= '<tr class="tr_title">
	<td id="periods'.$paymentNum.'">'.$paymentNum.'</td>
	<td>'.$nexttime.'</td>
	<td>'.$balance.'</td>
	<td id="repayment'.$paymentNum.'">'.$periodicPayment.'</td>
	<td id="benjin'.$paymentNum.'">'.$ben.'</td>
	<td id="lixinum'.$paymentNum.'">'.$paymentInterest.'</td>
	<td>'.$newBalance.'</td>';


	if($result['have_re_nums'] >= $paymentNum){
		$tablestr .= '<td><span class="green">已还款</span></td>';
	}else if($result['have_re_nums'] < $paymentNum && $nowtime < $nexttime){
		$tablestr .= '<td><span class="blue">未到期</span></td>';
	}else if($nowtime >= $nexttime){
		$tablestr .= '<td><a href="javascript:repayHandle('.$paymentNum.');">还款</a></td>';
	}
	$tablestr .= '</tr>';

	//return $tablestr;

	//本金没还完就继续调用
	if($result['style'] == 2){
		if ($newBalance > 0 && $paymentNum < $result['time_limit']) {
			$paymentNum++;//还款期数
			$tablestr .=amortizationTable($paymentNum,$periodicPayment,$newBalance,$monthlyInterest,$result);
			return $tablestr;
		}else{
			return $tablestr;
		}
	}else if($result['style'] == 3){
		if ($paymentNum < $result['time_limit']) {
			$paymentNum++;//还款期数
			$tablestr .=amortizationTable($paymentNum,$periodicPayment,$newBalance,$monthlyInterest,$result);
			return $tablestr;
		}else{
			return $tablestr;
		}
	}	
}

/** 计算分期还款，当前期应还款的各项数据
* $paymentNum--还款期数
* $balance--贷款本金
* $periodicPayment--每月还款额
* $paymentInterest--每月的利息
* $paymentPrincipal--每月本金减少额(偿还的本金)
* $monthlyInterest--月利率
* $style--还款方式 1到期还本付息；2等额本息，按月还款；3按月付息，到期还本。
* $end 是否最后一期还款 0否，1是
*/
function amortization($periodicPayment,$balance,$monthlyInterest,$style,$end=0){
	$resarr = array();
	if($style == 2){
		//round 四舍五入
		$resarr['paymentInterest'] = two_decimal_places($balance * $monthlyInterest);//每月的利息
		$resarr['paymentPrincipal'] = two_decimal_places($periodicPayment - $resarr['paymentInterest']);//每月本金减少额(偿还的本金)
		$resarr['newBalance'] = two_decimal_places($balance - $resarr['paymentPrincipal']);//下月计息的本金
		$resarr['ben'] = $periodicPayment - $resarr['paymentInterest']; //还本金
		$resarr['periodicPayment'] = $periodicPayment;//每月还款额
		if($end == 1){
			//最后一期还款时
			$resarr['ben'] = $balance+$resarr['newBalance'];
			$resarr['periodicPayment'] = $resarr['ben'] + $resarr['paymentInterest'];
			// $resarr['newBalance'] = 0;
		}
		// $newBalance = $balance - ($periodicPayment - $balance * $monthlyInterest)
	}else if($style == 3){
		//按月付息，到期还本
		// 每次（不包含最后一次）还款金额为：出借总额×月利率；
		// 最后一次还款金额为：出借总额×月利率 + 借款总额。
		$resarr['paymentInterest'] = two_decimal_places($periodicPayment);//每月的利息
		$resarr['paymentPrincipal'] = 0;//每月本金减少额(偿还的本金)
		$resarr['newBalance'] = two_decimal_places($balance - $resarr['paymentPrincipal']);//下月计息的本金
		$resarr['ben'] = $periodicPayment - $resarr['paymentInterest']; //还本金
		$resarr['periodicPayment'] = $periodicPayment;//每月还款额
		if($end == 1){
			//最后一期还款时
			$resarr['ben'] = $balance;
			$resarr['periodicPayment'] = $balance + $periodicPayment;
			$resarr['newBalance'] = 0;
		}
	}

	return $resarr;
}

/**
 * @description   随机打乱二维数组,传入二维数组,返回打乱后的二维数组
 * @author        
 * @date          2015-4-2
 */ 
function shuffle_twoarr($list) {
    if (!is_array($list)) return $list;
    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key) {
        $random[$key] = $list[$key];
    }  
    return $random;
}
