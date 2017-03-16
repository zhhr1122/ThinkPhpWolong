<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * use Common\Model 这块可以不需要使用，框架默认会加载里面的内容
 */
class CommonController extends Controller {


	public function __construct() {
	    header("Content-type:text/html; charset=utf-8");
		parent::__construct();
	}
	
	public function error($message = ''){
	    $message = $message?$message:'系统发生错误';
	    $this->assign('message',$message);
	    $this->display("Index/error");
	}
}