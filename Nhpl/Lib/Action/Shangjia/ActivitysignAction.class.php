<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */
class ActivitysignAction extends CommonAction{


    
    public  function index(){
       $Activitysign = D('Activitysign');
       import('ORG.Util.Page');// 导入分页类
       $map = array();
       $keyword = $this->_param('keyword','htmlspecialchars');
       if($keyword){
           $map['name|mobile'] = array('LIKE', '%'.$keyword.'%');
           $this->assign('keyword',$keyword);
       }
       $activity_id = (int)$this->_param('activity_id');
       if($activity_id){
           $map[C('DB_PREFIX').'activity_sign.activity_id'] = $activity_id;
       }
	   
	   $join = ' inner join '.C('DB_PREFIX').'activity a on a.activity_id = '.C('DB_PREFIX').'activity_sign.activity_id';
	   $map['shop_id'] = $this->shop_id;

       $count      = $Activitysign->join($join)->where($map)->count();// 查询满足要求的总记录数 
       $Page       = new Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
       $show       = $Page->show();// 分页显示输出
       $list = $Activitysign->join($join)->where($map)->order(array('sign_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
	   
	   //echo $Activitysign->getLastsql();
	   
       $activity_ids = array();
       foreach($list as  $val){
           $activity_ids[$val['activity_id']] = $val['activity_id'];
       }
       $this->assign('activity',D('Activity')->itemsByIds($activity_ids));
       $this->assign('list',$list);// 赋值数据集www.hatudou.com  二开开发qq  120585022
       $this->assign('page',$show);// 赋值分页输出
       $this->display(); // 输出模板
    }




    
   
}
