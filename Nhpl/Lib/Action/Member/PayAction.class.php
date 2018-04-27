<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class PayAction extends CommonAction {

    protected function ele_success($message, $detail) {
        $order_id = $detail['order_id'];
        $eleorder = D('Eleorder')->find($order_id);
        $detail['single_time'] = $eleorder['create_time'];
        $detail['settlement_price'] = $eleorder['settlement_price'];
        $detail['new_money'] = $eleorder['new_money'];
        $detail['fan_money'] = $eleorder['fan_money'];
        $addr_id = $eleorder['addr_id'];
        $product_ids = array();
        $ele_goods = D('Eleorderproduct')->where(array('order_id'=>$order_id))->select();
        foreach ($ele_goods as $k=>$val){
            if(!empty($val['product_id'])){
                $product_ids[$val['product_id']] = $val['product_id'];
            }
        }
        $addr = D('Useraddr')->find($addr_id);
        $this->assign('addr',$addr);
        $this->assign('ele_goods',$ele_goods);
        $this->assign('products',D('Eleproduct')->itemsByIds($product_ids));
        $this->assign('message',$message);
        $this->assign('detail',$detail);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('ele');
    }

    protected function hotel_success($message, $detail) {
        $order_id = (int)$detail['order_id'];
        $order = D('Hotelorder')->find($order_id);
        $detail['single_time'] = $order['create_time'];
        $room = D('Hotelroom')->find($order['room_id']);
        $hotel = D('Hotel')->find($room['hotel_id']);
        $this->assign('hotel',$hotel);
        $this->assign('order',$order);
        $this->assign('room',$room);
        $this->assign('message', $message);
        $this->assign('detail', $detail);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('hotel');
    }
    
    
    protected function farm_success($message, $detail) {
        $order_id = (int)$detail['order_id'];
        $order = D('FarmOrder')->find($order_id);
        $f = D('FarmPackage')->find($order['pid']);
        $shop = D('Shop')->find($farm['shop_id']);
        $farm = D('Farm')->where(array('farm_id'=>$f['farm_id']))->find();
        
        $this->assign('farm',$farm);
        $this->assign('order',$order);
        $this->assign('f',$f);
        $this->assign('shop', $shop);
        $this->assign('detail', $detail);
        $this->assign('message', $message);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('farm');
    }
    
    protected function goods_success($message, $detail) {
        $order_ids = array();
        if(!empty($detail['order_id'])){
            $order_ids[] = $detail['order_id'];
        }else{
            $order_ids = explode(',',$detail['order_ids']);
        }
        $goods = $good_ids = $addrs = array();
        $use_integral = 0;
        foreach($order_ids as $k=>$val){
            if(!empty($val)){
                $order = D('Order')->find($val);
                $addr = D('Useraddr')->find($order['addr_id']);
                $ordergoods = D('Ordergoods')->where(array('order_id'=>$val))->select();
                foreach($ordergoods as $a=>$v){
                    $good_ids[$v['goods_id']] = $v['goods_id'];
                    $use_integral += $v['use_integral'];
                }
            }
            $goods[$k] = $ordergoods;
            $addrs[$k] = $addr;
        }
        $this->assign('use_integral',$use_integral);
        $this->assign('addr',$addrs[0]);
        $this->assign('goods',$goods);
        $this->assign('good',D('Goods')->itemsByIds($good_ids));
        $this->assign('detail',$detail);
        $this->assign('message',$message);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('goods');
    }

	public function detail($order_id)
	{
		$dingorder = D('Shopdingorder');
		$dingyuyue = D('Shopdingyuyue');
		$dingmenu = D('Shopdingmenu');
		if(!$order = $dingorder->where('order_id = '.$order_id)->find()){
			$this->baoError('该订单不存在');
		}else if(!$yuyue = $dingyuyue->where('ding_id = '.$order['ding_id'])->find()){
			$this->baoError('该订单不存在');
		}else if($yuyue['user_id'] != $this->uid){
			$this->error('非法操作');
		}else{
			$arr = $dingorder->get_detail($this->shop_id,$order,$yuyue);
			$menu = $dingmenu->shop_menu($this->shop_id);
			$this->assign('yuyue', $yuyue);
			$this->assign('order', $order);
			$this->assign('order_id', $order_id);
			$this->assign('arr', $arr);
			$this->assign('menu', $menu);
			$this->display();
		}
	}

	protected function ding_success($message, $detail) {
        $order_id = (int)$detail['order_id'];
        $order = D('Shopdingorder')->find($order_id);
        $dingmenu = D('Shopdingordermenu')->where(array('order_id'=>$order_id))->select();
        $menu_ids = array();
        foreach($dingmenu as $k=>$val){
            $menu_ids[$val['menu_id']] = $val['menu_id'];
        }
        $this->assign('menus',D('Shopdingmenu')->itemsByIds($menu_ids));
        $this->assign('shop',D('Shopding')->find($order['shop_id']));
        $this->assign('dingmenu',$dingmenu);
        $this->assign('order',$order);
        $this->assign('message', $message);
        $this->assign('detail', $detail);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('ding');
    }
	
	 public function booking($order_id){
        $Bookingorder = D('Bookingorder');
        $Bookingyuyue = D('Bookingyuyue');
        $Bookingmenu = D('Bookingmenu');
        if (!($order = $Bookingdingorder->where('order_id = ' . $order_id)->find())) {
            $this->baoError('该订单不存在');
        } else {
            if (!($yuyue = $Bookingyuyue->where('ding_id = ' . $order['ding_id'])->find())) {
                $this->baoError('该订单不存在');
            } else {
                if ($yuyue['user_id'] != $this->uid) {
                    $this->error('非法操作');
                } else {
                    $arr = $Bookingorder->get_detail($this->shop_id, $order, $yuyue);
                    $menu = $Bookingmenu->shop_menu($this->shop_id);
                    $this->assign('yuyue', $yuyue);
                    $this->assign('order', $order);
                    $this->assign('order_id', $order_id);
                    $this->assign('arr', $arr);
                    $this->assign('menu', $menu);
                    $this->display();
                }
            }
        }
    }
	
	protected function booking_success($message, $detail) {
        $order_id = (int)$detail['order_id'];
        $order = D('Bookingorder')->find($order_id);
        $bookingordermenu = D('Bookingordermenu')->where(array('order_id'=>$order_id))->select();
        $menu_ids = array();
        foreach($bookingordermenu as $k=>$val){
            $menu_ids[$val['menu_id']] = $val['menu_id'];
        }
        $this->assign('menus',D('Bookingmenu')->itemsByIds($menu_ids));
        $this->assign('shop',D('Booking')->find($order['shop_id']));
        $this->assign('dingmenu',$dingmenu);
        $this->assign('order',$order);
        $this->assign('message', $message);
        $this->assign('detail', $detail);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('booking');
    }
   

    protected function other_success($message, $detail) {
        //dump($detail);
        $tuanorder = D('Tuanorder')->find($detail['order_id']);
        if(!empty($tuanorder['branch_id'])){
            $branch = D('Shopbranch')->find($tuanorder['branch_id']);
            $addr = $branch['addr'];
        }else{
            $shop = D('Shop')->find($tuanorder['shop_id']);
            $addr = $shop['addr'];
        }
        
        $this->assign('addr',$addr);
        $tuans = D('Tuan')->find($tuanorder['tuan_id']);
        $this->assign('tuans',$tuans);
        $this->assign('tuanorder',$tuanorder);
        $this->assign('message',$message);
        $this->assign('detail',$detail);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('other');
    }

    public function pay() {
        $logs_id = (int) $this->_get('logs_id');
        if (empty($logs_id)) {
            $this->error('没有有效的支付');
        }
      // if (!D('Lock')->lock($this->uid)) { //上锁
            //$this->error('服务器繁忙，1分钟后再试');
       // }
        if (!$detail = D('Paymentlogs')->find($logs_id)) {
          //  D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        if ($detail['code'] != 'money') {
          //  D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        $member = D('Users')->find($this->uid);

        if ($detail['is_paid']) {
          //  D('Lock')->unlock();
            $this->error('没有有效的支付');
        }
        if ($member['money'] < $detail['need_pay']) {
           // D('Lock')->unlock();
            $this->error('很抱歉您的账户余额不足', U('member/money/money'));
        }
        $member['money'] = $member['money'] - $detail['need_pay'];

        if (D('Users')->save(array('user_id' => $this->uid, 'money' => $member['money']))) {
            D('Usermoneylogs')->add(array(
                'user_id' => $this->uid,
                'money' => -$detail['need_pay'],
                'create_time' => NOW_TIME,
                'create_ip' => get_client_ip(),
                'intro' => '余额支付' . $logs_id,
            ));
            D('Payment')->logsPaid($logs_id);
           // D('Lock')->unlock();
            if ($detail['type'] == 'ele') {
                $this->ele_success('恭喜您支付成功啦！', $detail);
            }elseif ($detail['type'] == 'ding') {
                $this->ding_success('恭喜您支付成功啦！', $detail);
            }  elseif ($detail['type'] == 'booking') {
                $this->booking_success('恭喜您预订支付成功啦！', $detail);
            } elseif ($detail['type'] == 'goods') {
                $this->goods_success('恭喜您支付成功啦！', $detail);
            } elseif ($detail['type'] == 'hotel') {
                $this->hotel_success('恭喜您支付成功啦！', $detail);
            }elseif ($detail['type'] == 'farm') {
                $this->farm_success('恭喜您支付成功啦！', $detail);
            }elseif($detail['type'] == 'gold' ||$detail['type'] == 'money'||$detail['type'] == 'fzmoney'){
                $this->success('恭喜您充值成功',U('member/index/index'));die();
            } else {
                $this->other_success('恭喜您支付成功啦！', $detail);
            }

        }
    }

    //微信支付成功通知
    private function remainMoneyNotify($pay,$remain,$type=0)//0支出,1收入
    {
        //余额变动,微信通知
        $openid    = D('Connect')->getFieldByUid($this->uid,'open_id'); 
        $order_id  = $order['order_id'];
        $user_name = D('User')->getFieldByUser_id($this->uid,'nickname');
        if($type)
        $words     = "您的账户于".date('Y-m-d H:i:s')."收入".$pay."元,余额".$remain."元";
        else
        $words     = "您的账户于".date('Y-m-d H:i:s')."支出".$pay."元,余额".$remain."元";
        if($openid){
            $template_id = D('Weixintmpl')->getFieldByTmpl_id(4,'template_id');//余额变动模板
            $tmpl_data =  array(
                'touser'      => $openid,//用户微信openid
                'url'         => 'http://'.$_SERVER['HTTP_HOST'].'/mcenter',//相对应的订单详情页地址
                'template_id' => $template_id,
                'topcolor'    => '#2FBDAA',
                'data'        => array(
                    'first'=>array('value'=>'尊敬的用户,您的账户余额有变动！' ,'color'=>'#2FBDAA'),   
                    'keynote1'=>array('value'=> $user_name, 'color'=>'#2FBDAA'),//用户名
                    'keynote2'=>array('value'=> $words, 'color'=>'#2FBDAA'),//详情
                    'remark'  =>array('value'=>'详情请登录您的用户中心了解', 'color'=>'#2FBDAA')
                )
            );
            D('Weixin')->tmplmesg($tmpl_data);
        }
    }
}
