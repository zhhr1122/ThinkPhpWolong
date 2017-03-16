<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * use Common\Model 这块可以不需要使用，框架默认会加载里面的内容
 */
class LoginController extends Controller {

    public function index(){
        if(session('adminUser')){
            return $this->redirect('/admin.php?c=index');
        }else{  
            return $this->display();
        }  
    }
    //必须在服务端做一次校验
    public function check(){
       $username = $_POST['username'];
       $password = $_POST['password'];
       if(!trim($username)){
           return show(0, "用户不能为空", $data);
       }
       if(!trim($password)){
           return show(0, "密码不能为空", $data);
       }
       $ret = D('Admin')->getAdminByUserName($username);
       if(!$ret||$ret['status']!=1){
           return show(0,"该用户不存在");
       }
       if ($ret['password']!=getMD5Password($password)){
           return show(0, '密码错误');
       }
       session('adminUser',$ret);
       $ret['lastlogintime']=time();
       $id = $ret['admin_id'];
       unset($ret['admin_id']);
       $res = D("Admin")->updateAdminById($id,$ret);
       if(!$res){
           return show(0, '记录登陆时间错误');
       }else{
           return show(1, '登陆成功');
       }
       
    }
    
    public function Loginout(){
        session('adminUser',null);
        $this->redirect('/admin.php?c=login');//跳转重定向
    }

}