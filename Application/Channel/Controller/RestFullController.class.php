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

        $ret = $Dao->join('cms_channel_cid ON cms_channel_user.uname = cms_channel_cid.cname')->order('uname desc')->page($page,$step)->select();
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

    public function updateData(){
        $datas = $_POST['datas'];

        $User = M("channel_data");
        $ret = $User->where(array("dcid"=>$datas['dcid'],"ddate"=>$datas['ddate']))->save($datas);
        if($ret){
            $this->ajaxReturn(array("msg"=>"success"),'JSON');
        }else{
             $this->ajaxReturn(array("msg"=>"error","data"=>"无法更新"),'JSON');
        }

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

    //导出excel数据
    public function outputExcel() {

        //生成Excel的表头的索引
        $headIndex = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        vendor('PHPExcel');
        $User = M("channel_data");
        $ret = null;
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

        //所有字段，生成Excel的表头数据
        $headNames = $Dao->select();
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

//        $this->ajaxReturn(array("totalPage"=>$totalPage,"totalRecord"=>$totalRecord,"countAll"=>$countAll,
//                "msg"=>"success","records"=>$dcid),'JSON');

        // Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();
        // Set properties
        $objPHPExcel->getProperties()->setCreator("ctos")
                ->setLastModifiedBy("ctos")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        // set width

        $endIndex = 'A';
        for($i = 0;$i<count($headNames);$i++){
            $objPHPExcel->getActiveSheet()->getColumnDimension($headIndex[$i])->setWidth(20);
            $objPHPExcel->getActiveSheet()->getStyle($headIndex[$i])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $endIndex = $headIndex[$i];
        }


        // 设置行高度
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(22);

        // 字体和样式
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$endIndex.'2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.(count($ret)+3))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$endIndex.'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$endIndex.'2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        // 设置水平居中
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.(count($ret)+3))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //  合并
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$endIndex.'1');
        $objPHPExcel->getActiveSheet()->mergeCells('A'.(count($ret)+3).':'.$endIndex.(count($ret)+3));


        //合计数据
        $countAllStr = '合计：统计天数：'.count($ret).', ';
        for($i = 0;$i<count($countAll);$i++){
            $countAllStr = $countAllStr.$countAll[$i]['countName'].'：'.$countAll[$i]['countSum'].',  ';
        }

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.(count($ret)+3),$countAllStr );

        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', $dname.$dcid.'渠道数据表');
        for($i = 0;$i<count($headNames);$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($headIndex[$i].'2', $headNames[$i]['fname']);
        }

        // 内容
        for ($i = 0, $len = count($ret); $i < $len; $i++) {
        for($j = 0;$j<count($headNames);$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($headIndex[$j] . ($i + 3), $ret[$i][$headNames[$j]['ffield']]);
        }
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle($dcid.'渠道数据');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // 输出
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $dname.'-channel-'.$dcid . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}