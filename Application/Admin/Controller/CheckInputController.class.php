<?php
namespace Admin\Controller;
use Think\Controller;

class CheckInputController extends Controller
{
    function in($CnName, $name, $CheckType, $default = NULL, $LengMin = 0, $LengMax = 0)
    {
		$res=CheckInputFunc2($CnName, $name, $CheckType, $default, $LengMin, $LengMax);
		if($res['ok']==false){
			$this->error($res['error']);
		}
		return $res['data'];
	}
}
