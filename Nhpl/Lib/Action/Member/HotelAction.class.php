<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class HotelAction extends CommonAction {

  
    public function index() {
        $hotelorder = D('Hotelorder');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        if (($bg_date = $this->_param('bg_date', 'htmlspecialchars') ) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))) {
            $bg_time = strtotime($bg_date);
            $end_time = strtotime($end_date);
            $map['create_time'] = array(array('ELT', $end_time), array('EGT', $bg_time));
            $this->assign('bg_date', $bg_date);
            $this->assign('end_date', $end_date);
        } else {
            if ($bg_date = $this->_param('bg_date', 'htmlspecialchars')) {
                $bg_time = strtotime($bg_date);
                $this->assign('bg_date', $bg_date);
                $map['create_time'] = array('EGT', $bg_time);
            }
            if ($end_date = $this->_param('end_date', 'htmlspecialchars')) {
                $end_time = strtotime($end_date);
                $this->assign('end_date', $end_date);
                $map['create_time'] = array('ELT', $end_time);
            }
        }
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['order_id'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if (isset($_GET['online_pay']) || isset($_POST['online_pay'])) {
            $online_pay = (int) $this->_param('online_pay');
            if ($online_pay != 999) {
                $map['online_pay'] = $online_pay;
            }
            $this->assign('online_pay', $online_pay);
        } else {
            $this->assign('online_pay', 999);
        }
        $count = $hotelorder->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count,10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $hotelorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $room_ids  = $hotel_ids = array();
        foreach ($list as $k => $val) {
            $room_ids[$val['room_id']] = $val['room_id'];
            $hotel_ids[$val['hotel_id']] = $val['hotel_id'];
        }
        if (!empty($hotel_ids)) {
            $this->assign('hotels', D('Hotel')->itemsByIds($hotel_ids));
        }
        if($room_ids){
            $this->assign('rooms', D('Hotelroom')->itemsByIds($room_ids));
        }
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出    
        $this->display();
    }
    
    public function detail($order_id){
        if(!$order_id = (int)$order_id){
            $this->error('该订单不存在');
        }elseif(!$detail = D('Hotelorder')->find($order_id)){
            $this->error('该订单不存在');
        }elseif($detail['user_id'] != $this->uid){
            $this->error('非法的订单操作');
        }else{
           $detail['night_num'] = $this->diffBetweenTwoDays($detail['stime'],$detail['ltime']); 
           $detail['room'] = D('Hotelroom')->find($detail['room_id']); 
           $detail['hotel'] = D('Hotel')->find($detail['hotel_id']);
           $this->assign('detail',$detail);
           $this->assign('roomtype',D('Hotelroom')->getRoomType());
           $this->display();
        }
    }
    
    function diffBetweenTwoDays ($day1, $day2){
          $second1 = strtotime($day1);
          $second2 = strtotime($day2);

          if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
          }
          return ($second1 - $second2) / 86400;
    }

    
    public function cancel($order_id){
       if(!$order_id = (int)$order_id){
           $this->baoError('订单不存在');
       }elseif(!$detail = D('Hotelorder')->find($order_id)){
           $this->baoError('订单不存在');
       }elseif($detail['user_id'] != $this->uid){
           $this->baoError('非法操作订单');
       }else{
           if(false !== D('Hotelorder')->cancel($order_id)){
               $this->baoSuccess('订单取消成功',U('hotel/detail',array('order_id'=>$order_id)));
           }else{
               $this->baoError('订单取消失败');
           }
       }
    }
    
    
    public function comment($order_id) {
        if(!$order_id = (int) $order_id){
            $this->error('该订单不存在');
        }elseif(!$detail = D('Hotelorder')->find($order_id)){
            $this->error('该订单不存在');
        }elseif($detail['user_id'] != $this->uid){
            $this->error('非法操作订单');
        }elseif($detail['comment_status'] == 1){
            $this->error('已经评价过了');
        }else{
            if ($this->_Post()) {
                $data = $this->checkFields($this->_post('data', false), array('score', 'content'));
                $data['user_id'] = $this->uid;
                $data['hotel_id'] = $detail['hotel_id'];
                $data['order_id'] = $order_id;
                $data['score'] = (int) $data['score'];
                if (empty($data['score'])) {
                    $this->baoError('评分不能为空');
                }
                if ($data['score'] > 5 || $data['score'] < 1) {
                    $this->baoError('评分为1-5之间的数字');
                }
                $data['cost'] = (int) $data['cost'];
                $data['content'] = htmlspecialchars($data['content']);
                if (empty($data['content'])) {
                    $this->baoError('评价内容不能为空');
                }
                if ($words = D('Sensitive')->checkWords($data['contents'])) {
                    $this->baoError('评价内容含有敏感词：' . $words);
                }
                $data['create_time'] = NOW_TIME;
                $data['create_ip'] = get_client_ip();
                $photos = $this->_post('photos', false);
                if($photos){
                    $data['have_photo'] = 1;
                }
                
                if ($comment_id = D('Hotelcomment')->add($data)) {
                    $local = array();
                    foreach ($photos as $val) {
                        if (isImage($val))
                            $local[] = $val;
                    }
                    if (!empty($local)){
                        foreach($local as $k=>$val){
                            D('Hotelcommentpics')->add(array('comment_id'=>$comment_id,'photo'=>$val));
                        }
                    }
                    D('Hotelorder')->save(array('hotel_id'=>$detail['hotel_id'],'comment_status'=>1));
                    D('Users')->updateCount($this->uid, 'ping_num');
                    D('Hotel')->updateCount($detail['hotel_id'],'comments');
                    D('Hotel')->updateCount($detail['hotel_id'],'score',$data['score']);
                    $this->baoSuccess('恭喜您点评成功!', U('member/hotel/index'));
                }
                $this->baoError('点评失败！');
            }else {
                $this->assign('detail', $detail);
                $this->display();
            }
        }
    }

}
