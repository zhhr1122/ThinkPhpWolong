<?php
/**
 * 后台Index相关
 */
namespace Admin\Controller;
use Think\Controller;
class PositionController extends CommonController {

    public function index()
    {
        $data = array();
        $page = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
        $pageSize = $_REQUEST['pageSize'] ? $_REQUEST['pageSize'] : 10;
        $positions = D("Position")->getPosition($data, $page, $pageSize);
        $positionCount = D("Position")->getPositionCount($data);
        $res = new \Think\Page($positionCount, $pageSize);
        $pageRes = $res->show();
        $this->assign('pageRes', $pageRes);
        $this->assign('position', $positions); 
        $this->display();
    }

    public function add() {
    	if($_POST){
    	    if(!isset($_POST['name'])||!$_POST['name']){
    	        return show(0, '菜单名不能为空');
    	    }
    	    if(!isset($_POST['m'])||!$_POST['m']){
    	        return show(0, '模块名不能为空');
    	    }
    	    if(!isset($_POST['c'])||!$_POST['c']){
    	        return show(0, '控制器不能为空');
    	    }
    	    if(!isset($_POST['f'])||!$_POST['f']){
    	        return show(0, '方法名不能为空');
    	    }
    	    if($_POST['menu_id']){
    	        return $this->save($_POST);
    	    }
    	    $menuId =D("Menu")->insert($_POST);
    	    if($menuId){
    	        return show(1, '新增成功',$menuId); 
    	    }else{
    	        return show(0, '新增失败',$menuId);
    	    }
    	}else{
    	    $this->display();
    	}
    }
    public function edit(){
            $menuId = $_GET['id'];
            $data = D("Menu")->getDataFromId($menuId);
            $this->assign('data',$data);
            $this->display();
    }
    
    public function save($data){
        $menu_id = $data['menu_id'];
        unset($data['menu_id']);
        try{
            $id = D("Menu")->updateMenuById($menu_id,$data);
            if($id ==false){
                return show(0,'更新失败');
            }else{
                return show(1,'更新成功');
            }
        }catch (Exception $e){
            return show(0,$e->getMessage());
        }
    }
    
    public function setStatus(){
        try{
            if($_POST){
                $id = $_POST['id'];
                $status = $_POST['status'];
            }
            $id = D("Menu")->updateStatusById($id,$status);
            if($id == false){
                if($status == -1){
                    return show(0,'删除失败');
                }
                return show(0,'修改失败');
            }else{
                if($status == -1){
                    return show(0,'删除成功');
                }
                return show(1,'修改成功');
            }
        }catch (Exception $e){
            return show(0,$e->getMessage());
        }
        
        return show(0, "没有提交的数据");
    }
}