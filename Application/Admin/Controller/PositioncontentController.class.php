<?php
/**
 * 后台Index相关
 */
namespace Admin\Controller;
use Think\Controller;
class PositioncontentController extends CommonController {

    public function index()
    {
        $data = array();
        $tittle = $_GET['title'];
        if($tittle){
            $data['title']= $tittle;
            $this->assign('title',$data['title']);
        }
        //显示选择推荐位的结果
        if($_GET['position_id']){
            $position_id = intval($_GET['position_id']);
            $data['position_id']= $position_id;
            $this->assign('position_id',$position_id);
        }
        
        //获取推荐位
        $all_positions  = D("Position")->getAllPosition();
        $this->assign("all_positions",$all_positions);
        
        $page = $_REQUEST['p'] ? $_REQUEST['p'] : 1;
        $pageSize = $_REQUEST['pageSize'] ? $_REQUEST['pageSize'] : 5;
        $positions = D("PositionContent")->getPositioncontent($data, $page, $pageSize);
        $positionCount = D("PositionContent")->getPositioncontentCount($data);
        $res = new \Think\Page($positionCount, $pageSize);
        $pageRes = $res->show();
        $this->assign('pageRes', $pageRes);
        $this->assign('position', $positions); 
        $this->display();
    }

    public function add() {
    	if($_POST){
    	    if(!isset($_POST['title'])||!$_POST['title']){
    	        return show(0, '标题不能为空');
    	    }
    	    if(!isset($_POST['position_id'])||!$_POST['position_id']){
    	        return show(0, '请选择推荐位');
    	    }
    	    if(!$_POST['url']&&!$_POST['news_id']){
    	        return show(0, 'url和news_id不能同时为空');
    	    }
    	    if(!isset($_POST['thumb'])||!$_POST['thumb']){
    	       if($_POST['news_id']){
    	           $res = D("News")->getDataFromId($_POST['news_id']);
    	           if($res&&is_array($res)){
    	               $_POST['thumb'] = $res['thumb'];
    	           }
    	       }else{
    	           return show(0, '图片不能为空');
    	       }
    	       
    	    }
    	    if($_POST['id']){
    	        return $this->save($_POST);
    	    }
    	    try{
    	        $data = $_POST;
    	        $data['create_time'] = time();
    	        $positionId =D("PositionContent")->insert($data);
    	        if($positionId){
    	            return show(1, '新增成功',$positionId);
    	        }else{
    	            return show(0, '新增失败',$positionId);
    	        }
    	    }catch (Exception $e){
    	        return show(0, $e->getMessage());
    	    }
    	}else{
    	    $positions  = D("Position")->getAllPosition();
    	    $this->assign('positions', $positions);
    	    $this->display();
    	}
    }
    public function edit(){
            $positionId = $_GET['id'];
            if(!$positionId){
                //执行跳转
                $this->redirect('admin.php?c=position');
            }
            $data = D("PositionContent")->getDataFromId($positionId);
            if(!$data){
                //执行跳转
                $this->redirect('admin.php?c=position');
            }
            if($data['news_id']==0){
                unset($data['news_id']);
            }
            $positions  = D("Position")->getAllPosition();
            $this->assign('positions', $positions);
            $this->assign('data',$data);
            $this->display();
    }
    
    public function save($data){
        $id = $data['id'];
        unset($data['id']);
        try{
            $id = D("PositionContent")->updatePositionContentById($id,$data);
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
        $data = array(
            'id' => intval($_POST['id']),
            'status'=>intval($_POST['status']),
        );
        return parent::setStatus($data, 'PositionContent');
       /*  try{
            if($_POST){
                $id = $_POST['id'];
                $status = $_POST['status'];
            }
            $id = D("PositionContent")->updateStatusById($id,$status);
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
        
        return show(0, "没有提交的数据"); */
    }
    
    public function listorder(){
        $listorder = $_POST['listorder'];
        $errors = array();
        if($listorder){
            try {
                foreach ($listorder as $menuId =>$v){
                    $id = D("PositionContent")->updatePositionListorderById($menuId,$v);
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