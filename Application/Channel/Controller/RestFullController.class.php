<?php
namespace Channel\Controller;
use Think\Controller;
use Think\Model;
class RestFullController extends Controller {
    public function getUser(){

        //一页显示10条数据
        $step=10;
        //请求的页码
        $page = $_GET['page'];
        if(is_null($page)){
          $page = 1;
        }
        if( $page-1 >= 0){
            $page = $page -1;
        }
        //获得表中总记录数
        $Dao = M("channel_user");
        $ret = $Dao->select();
        $totalRecord = count($ret);


        //获取总分页数
        if($totalRecord%$step == 0){
            $totalPage = (integer)($totalRecord/$step);
        }else{
            $totalPage =(integer)($totalRecord/$step+1);
        }

        if($page <= $totalPage){
            $ret = $Dao->limit($page*$step,$page*$step+$step)->select();
        }


        $data["totalRecord"]=$totalRecord;

        $data["totalPage"]=$totalPage;

        $data["records"] = $ret;

        $this->ajaxReturn ($data,'JSON');
	}

	public function addUser(){
        $data['uname']= $_POST['uname'];
        $data['ucid'] = $_POST['ucid'];
        $data['uaccount'] = $_POST['uaccount'];
        $data['upassword'] = $_POST['upassword'];
        $data['uaddtime'] = date("Y-m-d");

        $condition1['ucid'] =  $data['ucid'];
        $condition2['uaccount'] =  $data['uaccount'];
        $User = M("channel_user"); // 实例化User对象
        if($User->where($condition1)->select()){
            redirect('/index.php/Channel/Index/index',3,'添加失败，相同渠道号,正在跳转。。。');
            return;
        }else if($User->where($condition2)->select()){
            redirect('/index.php/Channel/Index/index',3,'添加失败，相同账号,正在跳转。。。');
            return;
        }

        $User->add($data);
        redirect('/index.php/Channel/Index/index',2,'添加成功,正在跳转。。。');
	}

    public function deleteUser(){

        $ucid = $_POST['ucid'];

        if($ucid){
           $User = M("channel_user");
           $User->where('ucid='.$ucid)->delete();
           $this->ajaxReturn(array("msg"=>"success"),'JSON');
        }

    }

    public function changePass($ucid){
         $oldPassword = $_POST['change_cur_pass'];
         $newPassword =  $_POST['change_new_pass'];

         if(!is_null($ucid)){

            $User = M("channel_user");
            $con['ucid'] = $ucid;
            $ret = $User->where($con)->find();
            if($ret['upassword'] != trim($_POST['change_cur_pass'])){
                 redirect('/index.php/Channel/Index/index',3,'当前密码错误,正在返回。。。');
                 return;
            }
            $data['upassword'] = trim($newPassword);
            $User->where('ucid='.$ucid)->save($data);
            redirect('/index.php/Channel/Index/index',2,'修改密码成功,正在返回。。。');
         }else{
            redirect('/index.php/Channel/Index/index',3,'没有cid,正在返回。。。');
         }
    }

}