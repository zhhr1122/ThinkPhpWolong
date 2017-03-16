<?php
/**
 * 后台Index相关
 */
namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController {
    
    public function index(){
        $data = array();
        $page = $_REQUEST['p']?$_REQUEST['p']:1;
        $pageSize = $_REQUEST['pageSize']?$_REQUEST['pageSize']:10;
        $admin = D("Admin")->getAdmin($data,$page,$pageSize);
        $adminCount = D("Admin")->getAdminCount($data);
        $res = new \Think\Page($adminCount,$pageSize);
        $pageRes = $res->show();
        $this->assign('pageRes',$pageRes);
        $this->assign('admin',$admin);
        $this->display();
    }

    public function add() {
    	if($_POST){
    	    if(!isset($_POST['username'])||!$_POST['username']){
    	        return show(0, '用户名不能为空');
    	    }
    	    if(!isset($_POST['password'])||!$_POST['password']){
    	        return show(0, '密码不能为空');
    	    }
    	    if(!isset($_POST['password_re'])||!$_POST['password_re']||$_POST['password_re']!=$_POST['password']){
    	        return show(0, '两次输入密码不一致');
    	    }
    	    if(!isset($_POST['realname'])||!$_POST['realname']){
    	        return show(0, '真实姓名不能为空');
    	    }
    	    if($_POST['admin_id']){
    	        return $this->save($_POST);
    	    }
    	    $_POST['password']=getMD5Password($_POST['password']);
    	    $adminId =D("Admin")->insert($_POST);
    	    if($adminId){
    	        return show(1, '新增成功',$adminId); 
    	    }else{
    	        return show(0, '新增失败',$adminId);
    	    }
    	}else{
    	    $this->display();
    	}
    }
    
    public function personal(){
        $id = $_SESSION['adminUser']['admin_id'];
        $data =  D("Admin")->getDataFromId($id);
        $this->assign('data',$data);
        $this->display();
    }
    public function edit(){
            $menuId = $_GET['id'];
            $data = D("Menu")->getDataFromId($menuId);
            $this->assign('data',$data);
            $this->display();
    }
    
    public function save(){
        if($_POST){
    	    if(!isset($_POST['realname'])||!$_POST['realname']){
    	        return show(0, '真实姓名不能为空');
    	    }
    	    $admin_id = intval($_POST['admin_id']);
    	    unset($data['admin_id']);
    	    try{
    	        $res = D("Admin")->updateAdminById($admin_id,$_POST);
    	        if($res ==false){
    	            return show(0,'更新失败');
    	        }else{
    	            return show(1,'更新成功');
    	        }
    	    }catch (Exception $e){
    	        return show(0,$e->getMessage());
    	    }
    	}else{
    	    $this->display();
    	}
    }
    
    public function setStatus(){
        try{
            if($_POST){
                $id = $_POST['id'];
                $status = $_POST['status'];
            }
            $id = D("Admin")->updateStatusById($id,$status);
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
    
    public function listorder(){
        $listorder = $_POST['listorder'];
        $errors = array();
        if($listorder){
            try {
                foreach ($listorder as $menuId =>$v){
                    $id = D("Menu")->updateMenuListorderById($menuId,$v);
                    if($id==false){
                        $errors[] = $menuId;
                    }
                }
            } catch (Exception $e) {
                return show(0, $e->getMessage());
            }
            if(!$errors){
                return show(0, "排序失败".implode(',', $errors));
            }
            return show(1, "排序成功");
        }
        return show(0, "排序数据失败");
    }
   
}