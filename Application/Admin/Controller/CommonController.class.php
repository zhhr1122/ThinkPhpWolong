<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * use Common\Model 这块可以不需要使用，框架默认会加载里面的内容
 */
class CommonController extends Controller {


	public function __construct() {
		
		parent::__construct();
		$this->_init();
	}
	/**
	 * 初始化
	 * @return
	 */
	private function _init() {
		// 如果已经登录
		$isLogin = $this->isLogin();
		if(!$isLogin) {
			// 跳转到登录页面
			$this->redirect('/admin.php?c=login');
		}
	}

	/**
	 * 获取登录用户信息
	 * @return array
	 */
	public function getLoginUser() {
		return session("adminUser");
	}

	/**
	 * 判定是否登录
	 * @return boolean 
	 */
	public function isLogin() {
		$user = $this->getLoginUser();
		if($user && is_array($user)) {
			return true;
		}
		return false;
	}
	
	public function setStatus($data,$models){
	    try{
	        if($_POST){
	            $id = $data['id'];
	            $status = $data['status'];
	        }
	        $id = D($models)->updateStatusById($id,$status);
	        if($id == false){
	            if($status == -1){
	                return show(0,'删除失败');
	            }
	            return show(0,'修改失败');
	        }else{
	            if($status == -1){
	                return show(1,'删除成功');
	            }
	            return show(1,'修改成功');
	        }
	    }catch (Exception $e){
	        return show(0,$e->getMessage());
	    }
	
	    return show(0, "没有提交的数据");
	}

}