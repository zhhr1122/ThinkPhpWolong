<?php
namespace Common\Model;
use Think\Model;

/**
 * POSITION内容的model操作
 * @author 皓然
 *
 */
class PositionModel extends Model{
    private $_db = "";
    
    public function __construct(){
        $this->_db = M('position');
    }
    
    public function getAllPosition(){
        $data = array();
        $data['status'] =array('neq',-1);
        $list =$this->_db->where($data)->order('id desc')->select();
        return $list;
    }
    
    
    public function getPosition($data,$page,$pageSize = 10){
        $data['status'] =array('neq',-1);
        $offset = ($page-1)*$pageSize;
        $list =$this->_db->where($data)->order('id desc')->limit($offset,$pageSize)->select();
        return $list;
    }
    
    public function getPositionCount($data = array()){
        $data['status'] =array('neq',-1);
        return $this->_db->where($data)->count();
    }
    
    public function getPositionNameFromId($id){
        if(!$id||!is_numeric($id)){
            return array();
        }
        $ret = $this->_db->where('id='.$id)->find();
        return $ret['name'];
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
}