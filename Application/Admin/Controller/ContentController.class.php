<?php
/**
 * 后台Index相关
 */
namespace Admin\Controller;

use Think\Controller;

class ContentController extends CommonController
{

    public function index()
    {
        $conds = array();
        
       /*  if(isset($_REQUEST['catid'])){
            $data['catid'] = intval($_REQUEST['catid']);
            $this->assign('detail',$data['catid']);
        }
        if(isset($_REQUEST['title'])){
            $data['title'] = $_REQUEST['title'];
            
        } */
        //标题
        $tittle = $_GET['title'];
        if($tittle){
            $conds['title']= $tittle;
            $this->assign('title',$conds['title']);
        }
        //分类
        if($_GET['catid']){
            $conds['catid'] = intval($_GET['catid']);
            $this->assign('catid',$conds['catid']);
        }
        //显示选择推荐位的结果
        if($_GET['position_id']){
            $position_id = intval($_GET['position_id']);
            $this->assign('position_id',$position_id);
        }
        
        $BarMenuRes = D("Menu")->getBarMenus();
        $this->assign('barMenus',$BarMenuRes);
        
        $page = $_REQUEST['p']?$_REQUEST['p']:1;
        $pageSize = $_REQUEST['pageSize']?$_REQUEST['pageSize']:5;
        $news = D("News")->getNews($conds,$page,$pageSize);
        
        $positions  = D("Position")->getAllPosition();
        $newsCount = D("News")->getNewsCount($conds);
        $res = new \Think\Page($newsCount,$pageSize);
        $pageRes = $res->show();
        $this->assign('pageRes',$pageRes);
        $this->assign('news',$news);
        $this->assign("positions",$positions);
        $this->display();
    }
    

    public function add()
    {
        if($_POST){
            if(!isset($_POST['title'])||!$_POST['title']){
                return show(0, '标题不存在');
            }
            if(!isset($_POST['small_title'])||!$_POST['small_title']){
                return show(0, '短标题不存在');
            }
            if(!isset($_POST['catid'])||!$_POST['catid']){
                return show(0, '文章栏目不存在');
            }
            if(!isset($_POST['keywords'])||!$_POST['keywords']){
                return show(0, '关键字不存在');
            }
            if(!isset($_POST['content'])||!$_POST['content']){
                return show(0, 'content不存在');
            }
            if($_POST['news_id']){
                return $this->save($_POST);
            }
            $newsId = D("News")->insert($_POST);
            if($newsId){
                $newsContentData['content'] = $_POST['content'];
                $newsContentData['news_id'] = $newsId;
                $newsContentId=D("NewsContent")->insert($newsContentData);
                if($newsContentId){
                    return show(1, "新增成功");
                }else{
                    return show(1, "主表插入成功，附表插入失败");
                }
            }else{
                return show(0,'新增失败');
            }
        }else{
            $BarMenuRes = D("Menu")->getBarMenus();
            $tittleFontColor = C("TITLE_FONT_COLOR");
            $copyFrom = C("COPY_FROM");
            $this->assign('barMenus',$BarMenuRes);
            $this->assign('tittleFontColor',$tittleFontColor);
            $this->assign('copyFrom',$copyFrom);
            $this->display();
        }
    }
    
    public function save($data){
        $news_id = $data['news_id'];
        unset($data['news_id']);
        try{
            $newsId = D("News")->updateNewsById($news_id,$data);
            if($newsId){
                $newsContentData['content'] = $data['content'];
                $newsContentId=D("NewsContent")->updateNewsContentById($news_id,$newsContentData);
                if($newsContentId){
                    return show(1, "更新成功");
                }else{
                    return show(1, "主表更新成功，附表更新失败");
                }
            }else{
                return show(0,'更新失败');
            }
        }catch (Exception $e){
            return show(0,$e->getMessage());
        }
        
    }
    
    public function edit(){
        $newsId = $_GET['id'];
        if(!$newsId){
            //执行跳转
            $this->redirect('admin.php?c=content');
        }
        $data = D("News")->getDataFromId($newsId);
        if(!$data){
            //执行跳转
            $this->redirect('admin.php?c=content');
        }
        $dataContent = D("NewsContent")->getNewsContentFromId($newsId);
        if($dataContent){
            $data['content'] = $dataContent['content'];
        }
        $BarMenuRes = D("Menu")->getBarMenus();
        $tittleFontColor = C("TITLE_FONT_COLOR");
        $copyFrom = C("COPY_FROM");
        $this->assign('barMenus',$BarMenuRes);
        $this->assign('tittleFontColor',$tittleFontColor);
        $this->assign('copyFrom',$copyFrom);
        $this->assign('data',$data);
        $this->display();
    }
    
    public function setStatus(){
        try{
            if($_POST){
                $id = $_POST['id'];
                $status = $_POST['status'];
            }
            $id = D("News")->updateStatusById($id,$status);
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
                    $id = D("News")->updateNewsListorderById($menuId,$v);
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
    
    public function push(){
        $jumpUrl = $_SERVER['HTTP_REFERER'];
        $positionId = intval($_POST['position_id']);
        $newsId = $_POST['push'];
        if(!$newsId || !is_array($newsId)){
            return show(0, '请选择推荐位的文章ID进行推荐');
        }
        if(!$positionId){
            return show(0, '没有选择推荐位');
        }
        try{
            $news = D("News")->getNewsByNewsIdIn($newsId);
            if(!$news){
                return show(0, '没有相关内容');
            }
            foreach ($news as $new){
                $data = array(
                    'position_id'=>$positionId,
                    'title'=>$new['title'],
                    'thumb'=>$new['thumb'],
                    'news_id'=>$new['news_id'],
                    'status'=>1,
                    'create_time'=>$new['create_time'],
                );
                $position = D("PositionContent")->insert($data);
            }
        }catch (Exception $e){
            return show(0, $e->getMessage());
        }
        
        return show(1, '推荐成功',array('jump_url'=>$jumpUrl));
    }
}