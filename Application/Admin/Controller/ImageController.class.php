<?php
/**
 * 后台Index相关
 */
namespace Admin\Controller;

use Think\Controller;

class ImageController extends CommonController
{

    public function __construct()
    {
        
    }

    public function ajaxuploadimage(){
        $upload = D("UploadImage");
        $res = $upload->imageUpload();
        if($res==false){
            return show(0,'上传失败',"");
        }else{
            return show(1,'上传成功',$res);
        }
    }
    
    public function kindupload(){
        $upload = D("UploadImage");
        $res = $upload->upload();
        if($res==false){
            echo "false";
            echo $upload->getError();
            return showKind(1,'上传失败');
        }else{
            echo "success";
            echo $upload->getError();
            return showKind(0,$res);
        }
    }
}