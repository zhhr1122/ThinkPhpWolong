<?php
namespace Common\Model;
use Think\Model;

/**
 * 文章内容的model操作
 * @author 皓然
 *
 */
class NewsModel extends Model{
    private $_db = "";
    
    public function __construct(){
        $this->_db = M('news');
    }
    
    public function insert($data = array()){
        if (!is_array($data)||!$data){
            return 0;
        }
        
        $data['create_time'] = time();
        $data['username'] = getLoginUsername();
        return $this->_db->add($data);
    }
    
    public function getNews($data,$page,$pageSize = 10){
        $conditions = $data;
        if(isset($data['title'])&&$data['title']){
            $conditions['title'] = array('like','%'.$data['title'].'%');
        }
        if(isset($data['catid'])&&$data['catid']){
            $conditions['catid'] = intval($data['catid']);
        }
        $conditions['status'] =array('neq',-1);
        $offset = ($page-1)*$pageSize;
        $list =$this->_db->where($conditions)->order('listorder desc,news_id desc')->limit($offset,$pageSize)->select();
        return $list;
    }
    
    public function selectNews($data,$number){
        $data['status'] = array('neq',-1);
        $list =$this->_db->where($data)->order('listorder desc,news_id desc')->limit(0,$number)->select();
        return $list;
    }
    public function getRank($data=array(),$limit = 100){
        $data['status'] = array('neq',-1);
        $list =$this->_db->where($data)->order('count desc,news_id desc')->limit(0,$limit)->select();
        return $list;
    }
    
    public function getNewsCount($data = array()){
        $conditions = $data;
        if(isset($data['title'])&&$data['title']){
            $conditions['title'] = array('like','%'.$data['title'].'%');
        }
        if(isset($data['catid'])&&$data['catid']){
            $conditions['catid'] = intval($data['catid']);
        }
        $conditions['status'] =array('neq',-1);
        return $this->_db->where($conditions)->count();
    }
    
    public function getDataFromId($id){
        if(!$id||!is_numeric($id)){
            return array();
        }
        $ret = $this->_db->where('news_id='.$id)->find();
        return $ret;
    }
    
    public function updateNewsById($id,$data){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        if(!$data ||!is_array($data)){
            throw_exception('更新的数据不合法');
        }
        $data['update_time'] = time();
        $ret = $this->_db->where('news_id='.$id)->save($data);
        return $ret;
    }
    
    public function updateStatusById($id,$status){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        /* if(!$status||!is_numeric($status)||$status==0){
            throw_exception('状态不合法');
        } */
        $data['status'] = $status;
        $ret = $this->_db->where('news_id='.$id)->save($data);
        return $ret;
    }
    
    public function updateNewsListorderById($id,$listorder){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        $data = array('listorder' => intval($listorder),);
        return $this->_db->where('news_id='.$id)->save($data);
    }
    //根据ID搜索一连串的新闻列表
    public function getNewsByNewsIdIn($newsIds){
        if(!is_array($newsIds)){
            throw_exception("参数不合法");
        }
        $data = array(
            'news_id' =>array('in',implode(',', $newsIds)),
        );
        
        return $this->_db->where($data)->select();
    }
    
    public function updateCount($id,$count){
        if(!is_numeric($id)||!$id){
            throw_exception("ID不合法");
        }
        if(!is_numeric($count)||!$count){
            throw_exception("count不能为非数字");
        }
        $data['count'] = $count;
        return $this->_db->where('news_id='.$id)->save($data);
    }
    
    public function maxcount(){
        $data = array(
            'status'=>1,
        );
        return  $this->_db->where($data)->order('count desc')->limit(1)->find();
    }
}