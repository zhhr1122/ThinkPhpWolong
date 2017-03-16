<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index($type=""){
        //获取首页大图数据
        $topPicNews = D("PositionContent")->selectPositionContent(array('status'=>1,'position_id'=>2),1);
        //首页小图推荐
        $topSmallNews = D("PositionContent")->selectPositionContent(array('status'=>1,'position_id'=>3),3);
        //
        $listNews = D("News")->selectNews(array('status'=>1,'thumb'=>array('neq','')),30);
        
        $advNews = D("PositionContent")->selectPositionContent(array('status'=>1,'position_id'=>5),2);
        
        //获取排行
        $rankNews = D("News")->getRank(array(),10);
        $this->assign("result",array(
            'topPicNews' =>$topPicNews,
            'topSmallNews' =>$topSmallNews,
            'listNews'=>$listNews,
            'advNews' =>$advNews,
            'rankNews'=>$rankNews,
            'catId'=>0,//记录header的标记
        ));
        /**
         * 生成页面静态化
         */
        if($type=='buildHtml'){
            $this->buildHtml('index',HTML_PATH,'Index/index');
        }else{
            $this->display();
        }
    }
    
    public function build_html(){
        $res = $this->index('buildHtml');
        if(!$res){
            return show(1,'缓存成功');
        }else{
            return show(0,'缓存失败');
        }
    }
    
    public function crontab_bulid_html(){
        $res = $this->index('buildHtml');
    }
    
    public function getCount(){
        if(!$_POST){
            return show(0, '没有任何内容');
        }
        $newsIds = array_unique($_POST);
        try{
            $list = D("News")->getNewsByNewsIdIn($newsIds);
            if(!$list){
                return show(0, 'notdata');
            }else{
               $data = array();
               
               foreach ($list as $k=>$v){
                   $data[$v['news_id']]=$v['count'];
               }
               return show(1, 'success',$data); 
            }
        }catch (Exception $e){
            return show(0, $e->getMessage());
        }
    }
}