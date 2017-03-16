<?php
/**
 * 后台Index相关
 */
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    
    public function index(){
        $news = D('News')->maxcount();
        $newsCount = D('News')->getNewsCount(array('status'=>1));
        $positionCount = D('Position')->getPositionCount(array('status'=>1));
        $adminCount = D('Admin')->getLastLoginUsers();
        print_r($adminCount);
        $this->assign('news',$news);
        $this->assign('newsCount',$newsCount);
        $this->assign('adminCount',$adminCount);
        $this->assign('positionCount',$positionCount);
    	$this->display();
    }

    public function main() {
    	$this->display();
    }
}