<?php
namespace Channel\Controller;
use Think\Controller;
use Think\Model;
class IndexController extends Controller {
    public function index(){
        //$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
		if(session('adminUser')){
            return $this->redirect('/admin.php?c=index');
		}else{  
            return $this->display();
		}  

	}
	
	public function login(){
	   $uaccount = $_POST['account'];
       $password = $_POST['password'];
		$Dao = M("channel_user");	
       if(!trim($uaccount)){
         $this->show("账户不能为空");
		 return;
       }
       if(!trim($password)){
		$this->show("密码不能为空");
		 return;
       }
	  $ret = $Dao->where('uaccount="'.$uaccount.'"')->find();
	  if(!$ret){
		 $this->show("账户不存在");
		return;
	  }
	  if(trim($password) == $ret[upassword]){
		  $this->show("登录成功");
		  session('adminUser',$ret);
	  }else{
		  $this->show("密码不正确");
		  return;
	  }
	}
}