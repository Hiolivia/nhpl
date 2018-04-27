<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class FarmAction extends CommonAction {

  
    public function index() {
        $F = D('FarmOrder'); // 实例化User对象
        $gotime = I('gotime','','trim');
        $order_id = I('order_id',0,'trim,intval');
        $map = array();
        $map['user_id'] = $this->uid;
        if($gotime){
            $gotime = strtotime($gotime);
            $map['gotime']  = array('between',array($gotime,$gotime+86399));
        }
        if($order_id){
            $map['order_id'] = $order_id;
        }
        import('ORG.Util.Page');// 导入分页类
 
        $count  = $F->where($map)->count();// 查询满足要求的总记录数
        $Page   = new Page($count,25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show   = $Page->show();// 分页显示输出
        $list = $F->where($map)->order('order_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        
        foreach($list as $k => $v){
            $farm = D('Farm')->where(array('farm_id'=>$v['farm_id']))->find();
            $list[$k]['farm'] = $farm;
        }

        $this->assign('list',$list);// 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }
    
    public function detail($order_id){
        if(!$order_id = (int)$order_id){
            $this->error('该订单不存在');
        }elseif(!$detail = D('FarmOrder')->find($order_id)){
            $this->error('该订单不存在');
        }elseif($detail['user_id'] != $this->uid){
            $this->error('非法的订单操作');
        }else{ 
           $detail['package'] = D('HotelPackage')->where(array('pid'=>$detail['pid']))->find();
           $detail['farm'] = D('Farm')->where(array('farm_id'=>$detail['farm_id']))->find();
           print
           $this->assign('detail',$detail);
           $this->display();
        } 
    }
    

    public function cancel($order_id){
       if(!$order_id = (int)$order_id){
           $this->baoError('订单不存在');
       }elseif(!$detail = D('FarmOrder')->find($order_id)){
           $this->baoError('订单不存在');
       }elseif($detail['user_id'] != $this->uid){
           $this->baoError('非法操作订单');
       }else{
           if(false !== D('FarmOrder')->cancel($order_id)){
               $this->baoSuccess('订单取消成功',U('farm/detail',array('order_id'=>$order_id)));
           }else{
               $this->baoError('订单取消失败');
           }
       }
    }

}
