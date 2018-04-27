<?php


//分钟级别锁！一分钟只允许过一个用户一条请求 防止用户并发恶意请求
class LockModel extends CommonModel{
    protected $pk   = 'id';
    protected $tableName =  'lock';
    
    protected $id = 0;


    public function  lock($uid){
        $uid = (int)$uid;
        $t = date('mdHi',NOW_TIME);
        $this->id= $this->add(array('uid'=>$uid,'t'=>$t));
        return $this->id;
    }
    
    public function unlock(){
        return $this->delete($this->id);
    }
}