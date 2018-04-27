<?php


class WeixinmsgAction extends CommonAction{


    
    public  function index(){
       $Weixinmsg = D('Weixinmsg');
       import('ORG.Util.Page');// 导入分页类
       $map = array();
       $count      = $Weixinmsg->where($map)->count();// 查询满足要求的总记录数 
       $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
       $show       = $Page->show();// 分页显示输出
       $list = $Weixinmsg->where($map)->order(array('msg_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
       $this->assign('list',$list);// 赋值数据集www.hatudou.com  二开开发qq  120585022
       $this->assign('page',$show);// 赋值分页输出
       $this->display(); // 输出模板
    }

    

    
   
}
