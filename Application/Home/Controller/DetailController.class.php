<?php
namespace Home\Controller;
use Think\Controller;
class DetailController extends Controller{
    public function index(){
        $id = intval($_GET['id']);
        if(!$id||$id<0){
            return $this->error('ID不合法');
        }
        $news = D("News")->getDataFromId($id);
        if(!$news||$news['status']!=1){
            //执行跳转
            return $this->error('ID不合法或者咨询被关闭');
        }
        $count = intval($news['count']+1);
        D("News")->updateCount($id,$count);
        $dataContent = D("NewsContent")->getNewsContentFromId($id);
        if($dataContent){
            $news['content'] = htmlspecialchars_decode($dataContent['content']);
        }
        $rankNews = D("News")->getRank(array(),10);
        $advNews = D("PositionContent")->selectPositionContent(array('status'=>1,'position_id'=>5),2);
        $this->assign("result",array(
            'advNews' =>$advNews,
            'rankNews'=>$rankNews,
            'catId'=>$news['catid'],//记录header的标记
            'news'=>$news,
        ));
        $this->assign("news",$news);
        $this->display("Detail/index");
    }
    
    public function view(){
        if(!getLoginUsername()){
            $this->error("您没有权限访问该页面");
        }
        $this->index();
    }
    public function error($message = ''){
        $message = $message?$message:'系统发生错误';
        $this->assign('message',$message);
        $this->display("Index/error");
    }
}