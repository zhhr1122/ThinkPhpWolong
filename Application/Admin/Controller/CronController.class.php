<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Upload;
class CronController{
    public function dumpmysql(){
        $shell = "mysqldump -u".C("DB_USER")." " .C("DB_NAME")." > /tmp/cms".date("Ymd")."sql";
        echo $shell;
    }
}

