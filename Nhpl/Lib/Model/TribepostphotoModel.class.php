<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TribepostphotoModel extends CommonModel{
    protected $pk   = 'photo_id';
    protected $tableName =  'tribe_post_photo';
    
    public function upload($post_id,$photos){
        $post_id = (int)$post_id;
        $this->delete(array("where"=>array('post_id'=>$post_id)));
        foreach($photos as $val){
            $this->add(array('photo'=>$val,'post_id'=>$post_id));
        }
        return true;
    }
    public function getbypost_ids($post_ids){
		$post_ids = $post_ids;
		$sql = "SELECT * FROM `bao_tribe_post_photo` WHERE ( `post_id` IN (". $post_ids . " ) )" ;
		$ret = $this->query($sql);
		return $ret;
	}
}