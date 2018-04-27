<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TribecollectModel extends CommonModel{
    protected $pk   = 'tribe_id,user_id';
    protected $tableName =  'tribe_collect';
    
    
    public function check($tribe_id,$user_id){
        $data = $this->find(array('where'=>array('tribe_id'=>(int)$tribe_id,'user_id'=>(int)$user_id)));
        return $this->_format($data);
    }
    
}