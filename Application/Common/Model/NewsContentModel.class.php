<?php
namespace Common\Model;
use Think\Model;

/**
 * 文章内容的model操作
 * @author 皓然
 *
 */
class NewsContentModel extends Model{
    private $_db = "";
    
    public function __construct(){
        $this->_db = M('news_content');
    }
    
    public function insert($data = array()){
        if (!is_array($data)||!$data){
            return 0;
        }
        
        $data['create_time'] = time();
        if(isset($data['content']) && $data['content']){
            $data['content'] = htmlspecialchars( $data['content']);//转换成html实体类
        }
        return $this->_db->add($data);
    }
    
    public function getNewsContentFromId($id){
        if(!$id||!is_numeric($id)){
            return array();
        }
        $ret = $this->_db->where('news_id='.$id)->find();
        return $ret;
    }
    
    public function updateNewsContentById($id,$data){
        if(!$id||!is_numeric($id)){
            throw_exception('ID不合法');
        }
        if(!$data ||!is_array($data)){
            throw_exception('更新的数据不合法');
        }
        if(isset($data['content']) && $data['content']){
            $data['content'] = htmlspecialchars( $data['content']);//转换成html实体类
        }
        $data['update_time'] = time();
        $ret = $this->_db->where('news_id='.$id)->save($data);
        return $ret;
    }
}