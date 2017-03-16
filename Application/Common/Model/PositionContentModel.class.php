<?php
namespace Common\Model;
use Think\Model;

/**
 * POSITION内容的model操作
 * @author 皓然
 *
 */
class PositionContentModel extends Model{
    private $_db = "";
    
    public function __construct(){
        $this->_db = M('position_content');
    }
    
    public function insert($data = array()){
        if (!is_array($data)||!$data){
            return 0;
        }
        if(!$data ||!is_array($data)){
            throw_exception('数据不合法');
        }
       /*  if(!data['create_time']){
            data['create_time'] = time();
        } */
        return $this->_db->add($data);
    }
    public function getPositioncontent($data,$page,$pageSize = 10){
        $data['status'] =array('neq',-1);
        $conditions = $data;
        if(isset($data['title'])&&$data['title']){
            $conditions['title'] = array('like','%'.$data['title'].'%');
        }
        if(isset($data['position_id'])&&$data['position_id']){
            $conditions['position_id'] = intval($data['position_id']);
        }
        $offset = ($page-1)*$pageSize;
        $list =$this->_db->where($conditions)->order('listorder desc,id desc')->limit($offset,$pageSize)->select();
        return $list;
    }
    
    public function selectPositionContent($data,$number){
        $data['status'] =array('neq',-1);
        $conditions = $data;
        $list =$this->_db->where($conditions)->limit(0,$number)->select();
        return $list;
    }
    
    public function getPositioncontentCount($data = array()){
        $data['status'] =array('neq',-1);
        $conditions = $data;
        if(isset($data['title'])&&$data['title']){
            $conditions['title'] = array('like','%'.$data['title'].'%');
        }
        if(isset($data['position_id'])&&$data['position_id']){
            $conditions['position_id'] = intval($data['position_id']);
        }
        return $this->_db->where($data)->count();
    }
    
    public function getDataFromId($id){
        if(!$id||!is_numeric($id)){
            return array();
        }
        $ret = $this->_db->where('id='.$id)->find();
        return $ret;
    }
    
    public function updatePositionContentById($id,$data){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        if(!$data ||!is_array($data)){
            throw_exception('更新的数据不合法');
        }
        $data['update_time'] = time();
        $ret = $this->_db->where('id='.$id)->save($data);
        return $ret;
    }
    
    public function updateStatusById($id,$status){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        $data['status'] = $status;
        $ret = $this->_db->where('id='.$id)->save($data);
        return $ret;
    }
    
    public function updatePositionListorderById($id,$listorder){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        $data = array('listorder' => intval($listorder),);
        return $this->_db->where('id='.$id)->save($data);
    }
}