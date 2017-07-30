<?php
namespace Channel\Controller;
use Think\Controller;
use Think\Model;
use Think\Db;
class RestFullController extends Controller {
    public function getUser(){

        //一页显示10条数据
        $step=10;
        //请求的页码
        $page = $_GET['page'];
        if(is_null($page) || $page == 0){
          $page = 1;
        }
        //获得表中总记录数

        $Dao = M("channel_user");

        $ret = $Dao->join('cms_channel_cid ON cms_channel_user.uname = cms_channel_cid.cname')->select();

        $totalRecord = count($ret);

        //获取总分页数
        if($totalRecord%$step == 0){
            $totalPage = (integer)($totalRecord/$step);
        }else{
            $totalPage =(integer)($totalRecord/$step+1);
        }
        if($page <= $totalPage){

        $ret = $Dao->join('cms_channel_cid ON cms_channel_user.uname = cms_channel_cid.cname')->page($page,$step)->select();
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


        $User = M("channel_user"); // 实例化User对象
        if($User->where(array("uname"=>$data['uname']))->select()){
            $this->ajaxReturn(array("msg"=>"error","data"=>"已有相同渠道名"),'JSON');
        }else if($User->where(array("uaccount"=>$data['uaccount']))->select()){
            $this->ajaxReturn(array("msg"=>"error","data"=>"已有相同账户名"),'JSON');
        }

        if($User->add($data)){
            $CDao = M('channel_cid');

            $cdata['cname'] = $data['uname'];
            $cdata['cid'] = $data['ucid'];

            if($CDao->where(array("cid"=>$cdata['cid']))->select()){
                $this->ajaxReturn(array("msg"=>"error","data"=>"已有相同渠道号"),'JSON');
            }
            if($CDao->add($cdata)){
                $this->ajaxReturn(array("msg"=>"success","data"=>""),'JSON');
            }
        }
        $this->ajaxReturn(array("msg"=>"error","data"=>"数据库操作失败"),'JSON');
	}


    public function deleteUser(){

        $ucid = $_POST['ucid'];

        if($ucid){
           $User = M("channel_cid");

           $users = $User->where('cid='.$ucid)->find();
           $countName = $User->where(array("cname"=>$users['cname']))->count();
//           $this->ajaxReturn(array("msg"=>"error","data"=>$countName),'JSON');
           //如果渠道表中的记录只剩下一条了，那就把这个user删除
           if($users && $countName == 1){
                $CDao = M('channel_user');
                $CDao->where(array("uname"=>$users['cname']))->delete();
                $User->where('cid='.$ucid)->delete();
           }else{
                $User->where('cid='.$ucid)->delete();
           }
           $this->ajaxReturn(array("msg"=>"success"),'JSON');

        }else{
           $this->ajaxReturn(array("msg"=>"error"),'JSON');
        }

    }

    public function changePass($uaccount){
         $oldPassword = $_POST['change_cur_pass'];
         $newPassword =  $_POST['change_new_pass'];

         if(!is_null($uaccount)){
            $User = M("channel_user");
            $con['uaccount'] = $uaccount;
            $ret = $User->where($con)->find();
            if($ret['upassword'] != trim($_POST['change_cur_pass'])){
                 redirect('/index.php/Channel/Index/index',3,'当前密码错误,正在返回。。。');
                 return;
            }
            $data['upassword'] = trim($newPassword);
            $User->where(array("uaccount"=>$uaccount))->save($data);
            redirect('/index.php/Channel/Index/index',2,'修改密码成功,正在返回。。。');
         }else{
            redirect('/index.php/Channel/Index/index',3,'没有cid,正在返回。。。');
         }
    }

    public function getAllUser(){

        $User = M("channel_user");
        $map['uaccount']  = array('neq',"wolongroot");
        $ret = $User->field('uname')->where($map)->select();
        $this->ajaxReturn ($ret,'JSON');
    }

    //添加渠道号
    public function addChannel(){
            $cname = $_POST['cname'];
            $new_channel = $_POST['new_channel'];
            if($cname && $new_channel){
               $User = M("channel_cid");
               if($User->where(array("cid"=>$new_channel))->select()){
                    $this->ajaxReturn(array("msg"=>"error","data"=>"已有相同渠道号"),'JSON');
               }
               $data['cname'] = $cname;
               $data['cid'] = $new_channel;
               $User->add($data);
               $this->ajaxReturn(array("msg"=>"success"),'JSON');
            }
    }

    public function getCid(){
         $cname = $_POST['cname'];
         $User = M("channel_cid");
         $ret = $User->where(array("cname"=>$cname))->select();
         $this->ajaxReturn($ret,'JSON');
    }

    //添加数据
    public function addData(){

        //提交的数据数组
        $datas = $_POST['datas'];

        $User = M("channel_data");
        $ret = $User->where(array("dcid"=>$datas['dcid'],"ddate"=>$datas['ddate']))->select();
        if($ret){
             $this->ajaxReturn(array("msg"=>"error","data"=>"已有当天数据，请勿重复添加"),'JSON');
        }
        $User->add($datas);
        $this->ajaxReturn(array("msg"=>"success"),'JSON');
    }

    //获取数据

    public function getData(){
        $User = M("channel_data");


        $startTime = $_GET['startTime'];
        $endTime = $_GET['endTime'];

        if(!$startTime){
            $startTime=date('Y-m-d');
        }
        if(!$endTime){
            $endTime=date('Y-m-d');
        }

        $dname = $_GET['dname'];
        $dcid = $_GET['dcid'];
        //查询的天数
        $days = $_GET['days'];

        //一页显示10条数据
        $step=10;
        //请求的页码
        $page = $_GET['page'];
        if(is_null($page) || $page == 0){
          $page = 1;
        }

        $map['dname']  = $dname;
        //如果是最近时间查询
        if($days != -1){
            $time=time()-$days*24*3600;
            $startDay=date('Y-m-d',$time);
            $map['ddate']  = array('egt',$startDay);

        }else{//如果是区间查询
            $map['ddate']  = array(array('egt',$startTime),array('elt',$endTime));
        }

        if($dcid == "全部"){
            $ret = $User->where($map)->order('ddate desc')->select();
         }else{
            $map['dcid'] = $dcid;
            $ret = $User->where($map)->order('ddate desc')->select();
         }


         $Dao = M("channel_field");

        $mapField['ffield']  = array('not in',array('dname','dcid','ddate'));
        $fields = $Dao->where($mapField)->select();
        $countAll =array();

        //获取需要统计字段的合计数值，返回一个map例如， 字段：1000
        foreach ($fields as $field){
              $temp['countName']=$field['fname'];
              $sum = 0;
              foreach ($ret as $value){
                 $sum += $value[''.$field['ffield']];
              }
              $temp['countSum']=$sum;
              array_push($countAll,$temp);
        }
//        $countAll = $User->where($map)->sum('dincome');
        $totalRecord = count($ret);

        //获取总分页数
        if($totalRecord%$step == 0){
            $totalPage = (integer)($totalRecord/$step);
        }else{
            $totalPage =(integer)($totalRecord/$step+1);
        }
        if($page <= $totalPage){
            if($dcid == "全部"){
                $ret = $User->where($map)->page($page,$step)->order('ddate desc')->select();
            }else{
                $map['dcid'] = $dcid;
                $ret = $User->where($map)->page($page,$step)->order('ddate desc')->select();
            }

        }

        $this->ajaxReturn(array("totalPage"=>$totalPage,"totalRecord"=>$totalRecord,"countAll"=>$countAll,
        "msg"=>"success","records"=>$ret),'JSON');
    }

    //删除数据
    public function deleteChannelData(){
           $map['ddate'] = $_POST['ddate'];
           $map['dcid'] = $_POST['dcid'];

           $Dao = M("channel_data");

           $ret = $Dao->where($map)->delete();
           if($ret){
                $this->ajaxReturn(array("msg"=>"success"),'JSON');
           }else{
                $this->ajaxReturn(array("msg"=>"error","data"=>"删除失败"),'JSON');
           }
    }
    //获取表头的名称数据
    public function getTableHead(){

        $flag = $_GET['flag'];

        $Dao = M("channel_field");

        if($flag == 1){//排除不能删除的字段
            $map['ffield']  = array('not in',array('dname','dcid','ddate'));
            $ret = $Dao->where($map)->select();
        }else{//全部查询
            $ret = $Dao->select();
        }


        $this->ajaxReturn($ret,'JSON');

    }


    //向data表插入一个新的float类型字段
    public function addField(){

        $fname = $_POST['fname'];
        $ffield = $_POST['ffield'];

        $Dao = M("channel_field");

        $ret = $Dao->where(array('ffield'=>$ffield))->select();

        if(!$ret){//没有相同记录
            $Model = new Model(); // 实例化一个model对象 执行sql语句
            $sql = "alter table cms_channel_data add ".$ffield." float(10,2) DEFAULT 0;";
            if(!$Model->execute($sql)){
                $this->ajaxReturn(array("msg"=>"error","data"=>"插入字段失败"),'JSON');
            }
            $Dao->add(array("ffield"=>$ffield,"fname"=>$fname));
            $this->ajaxReturn(array("msg"=>"success"),'JSON');
        }else{
            $this->ajaxReturn(array("msg"=>"error","data"=>"已有相同字段名"),'JSON');
        }

    }

    //删除data表字段
    public function deleteField(){
       $ffield = $_POST['ffield'];

       $Dao = M("channel_field");

       $ret = $Dao->where(array('ffield'=>$ffield))->select();

       if(!$ret){//没有记录
           $this->ajaxReturn(array("msg"=>"error","data"=>"没有该字段"),'JSON');
       }else{
           $Model = new Model(); // 实例化一个model对象 执行sql语句
                  $sql = "ALTER TABLE `cms_channel_data` DROP `".$ffield."`";
                  if(!$Model->execute($sql)){
                      $this->ajaxReturn(array("msg"=>"error","data"=>"删除字段失败"),'JSON');
                  }
                  $Dao->where(array("ffield"=>$ffield))->delete();
                  $this->ajaxReturn(array("msg"=>"success"),'JSON');
       }
    }

}