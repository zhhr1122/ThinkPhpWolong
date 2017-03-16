<?php
namespace Home\Controller;
use Think\Controller;
class CatController extends Controller{
    public function index(){
        $id = intval($_GET['id']);
        if(!$id){
            return $this->error('ID不存在');
        }
        $nav = D("Menu")->getDataFromId($id);
        if(!$nav||$nav['status']!=1){
            return $this->error('栏目ID不存在或者状态不正常');
        }
        $rankNews = D("News")->getRank(array(),10);
        $advNews = D("PositionContent")->selectPositionContent(array('status'=>1,'position_id'=>5),2);
        
        $page = $_REQUEST['p']?$_REQUEST['p']:1;
        $conds = array(
            'status'=>1,
            'thumb'=> array('neq',''),
            'catid'=>$id,
        );
        $pageSize = $_REQUEST['pageSize']?$_REQUEST['pageSize']:5;
        $news = D("News")->getNews($conds,$page,$pageSize);
        $newsCount = D("News")->getNewsCount($conds);
        $res = new \Think\Page($newsCount,$pageSize);
        $pageRes = $res->show();
        //$this->assign('pageRes',$pageRes);
        /* $this->assign('news',$news); */
        
        $this->assign("result",array(
            'advNews' =>$advNews,
            'rankNews'=>$rankNews,
            'catId'=>$id,//记录header的标记
            'pageRes'=>$pageRes,
            'listNews'=>$news,
        ));
        $this->display();
    }
    public function error($message = ''){
        $message = $message?$message:'系统发生错误';
        $this->assign('message',$message);
        $this->display("Index/error");
    }
}