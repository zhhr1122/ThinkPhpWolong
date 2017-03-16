<?php
namespace Common\Model;
use Think\Model;

class AdminModel extends Model{
    private $_db = '';
    public function __construct(){
        $this->_db = M('admin');
    }
    public function getAdminByUserName ($username){
        $ret = $this->_db->where('username="'.$username.'"')->find();
        return $ret;
    }
    
    public function getAdmin($data,$page,$pageSize = 10){
        $conditions = $data;
        $conditions['status'] =array('neq',-1);
        $offset = ($page-1)*$pageSize;
        $list =$this->_db->where($conditions)->order('admin_id desc')->limit($offset,$pageSize)->select();
        return $list;
    }
    public function getAdminCount($data = array()){
        $data['status'] =array('neq',-1);
        return $this->_db->where($data)->count();
    }
    
    public function updateStatusById($id,$status){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        $data['status'] = $status;
        $ret = $this->_db->where('admin_id='.$id)->save($data);
        return $ret;
    }
    
    
    public function updateAdminById($id,$data){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        if(!$data ||!is_array($data)){
            throw_exception('更新的数据不合法');
        }
        $ret = $this->_db->where('admin_id='.$id)->save($data);
        return $ret;
    }
    
    public function insert($data = array()){
        if(!$data || !is_array($data)){
            return 0;
        }
        $data['lastlogintime']=time();
        return $this->_db->add($data);
    }
    
    public function getDataFromId($id){
        if(!$id||!is_numeric($id)){
            return array();
        }
        $ret = $this->_db->where('admin_id='.$id)->find();
        return $ret;
    }
    
    public function getLastLoginUsers(){
        $time = mktime(0,0,0,date("m"),date("d"),date("Y"));//今日起始时间
        $data = array(
            'status'=>1,
            'lastlogintime'=>array('gt',$time),//"gt"表示大于今日起始时间
        );
        $res = $this->_db->where($data)->count();
        return $res;
    }
}
?>