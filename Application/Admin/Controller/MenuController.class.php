<?php
/**
 * 后台Index相关
 */
namespace Admin\Controller;
use Think\Controller;
class MenuController extends CommonController {
    
    public function index(){
       /*  if($_POST){
            $id = $_POST['menu_id'];
            return 	$this->delete($id);
        }else{ */
            $data = array();
            if(isset($_REQUEST['type'])&&in_array($_REQUEST['type'], array(0,1))){
                $data['type'] = intval($_REQUEST['type']);
                $this->assign('type',$data['type']);
            }else{
                $this->assign('type',-1);
            }
            /**
             * 分页操作逻辑
             */
            $page = $_REQUEST['p']?$_REQUEST['p']:1;
            $pageSize = $_REQUEST['pageSize']?$_REQUEST['pageSize']:10;
            $menus = D("Menu")->getMenus($data,$page,$pageSize);
            $menuCount = D("Menu")->getMenusCount($data);
             
            $res = new \Think\Page($menuCount,$pageSize);
            $pageRes = $res->show();
            $this->assign('pageRes',$pageRes);
            $this->assign('menus',$menus);
            $this->display();
       /*  } */
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
    
    public function delete($meun_id){
        try{
            $id = D("Menu")->DeleteFromId($meun_id);
            if($id == false){
                return show(0,'删除失败');
            }else{
                return show(1,'删除成功');
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