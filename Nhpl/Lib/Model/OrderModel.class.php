<?php
class OrderModel extends CommonModel{
	
    protected $pk = 'order_id';
    protected $tableName = 'order';
    protected $types = array(
		0 => '等待付款', 
		1 => '等待发货', 
		2 => '仓库已捡货', 
		3 => '客户已收货', 
		4 => '申请退款中', //待开发
		5 => '已退款', //待开发
		6 => '申请售后中', //待开发
		7 => '已完成售后', //待开发
		8 => '已完成配送'
	);
	
	
	
    public function getType(){
        return $this->types;
    }
	public function getError() {
        return $this->error;
    }
	
	public function order_delivery($order_id, $type=''){
		$order_id = (int)$order_id;
        $type = (int)$type;
		if($type ==0){
			$obj = D('Order');
		}else{
			$obj = D('Eleorder');	
		}
		$order_shop = $obj->where('order_id =' . $order_id)->find();
		$shop = D('Shop')->find($order_shop['shop_id']);
	
		if($shop['is_pei'] == 0) {//如果走配送
			$DeliveryOrder = D('DeliveryOrder')->where(array('type_order_id' => $order_id, 'type' => $type))->find();
			if (!empty($DeliveryOrder)) {
				if ($DeliveryOrder['closed'] ==0 ) {//如果订单状态是关闭
					D('DeliveryOrder')->where(array('type_order_id' => $order_id, 'type' => $type))->setField('closed', 0); //重新开启订单
				}else{
					if($DeliveryOrder['status'] == 2 || $DeliveryOrder['status'] == 8) {
						return false;
					}else{
						D('DeliveryOrder')->where(array('type_order_id' => $order_id, 'type' => 0))->setField('closed', 1);//更改配送状态
					}	
			   }
			}else{
				return false;
			}
		  return true;
		}	
	   return true;
		
	}
	
		
   //更新购物表的状态
   public function del_order_goods_closed($order_id) {
       $order_id = (int) $order_id;
       $order_goods = D('Ordergoods')->where(array('order_id' => $order_id))->select();
			foreach ($ordergoods as $k => $v){
				D('Ordergoods')->save(array('order_id' => $v['order_id'], 'closed' => 1)); 
        }
      return TRUE;
    }
	
  //更新退款库存
   public function del_goods_num($order_id) {
       $order_id = (int) $order_id;
       $ordergoods = D('Ordergoods')->where('order_id =' . $order_id)->select();
       foreach ($ordergoods as $k => $v) {
       	 D('Goods')->updateCount($v['goods_id'], 'num', $v['num']);
       }
      return TRUE;
    }
	
    //可以使用积分 根据订单使用积分的情况 返回支付记录需要实际支付的金额！
    public function useIntegral($uid, $order_ids){
        $orders = $this->where(array('order_id' => array('IN', $order_ids)))->select();
        $users = D('Users');
        $member = $users->find($uid);
        $useint = $fan = $total = 0;
        foreach ($orders as $k => $order) {
            if ($order['use_integral'] > $order['can_use_integral']) {//需要返回积分给客户
                $member['integral'] += $order['use_integral'] - $order['can_use_integral'];
                $this->save($order);//保存ORDER
                $users->addIntegral($uid, $order['use_integral'] - $order['can_use_integral'], '商城购物使用积分退还');//积分退还
                $orders[$k]['use_integral'] = $order['use_integral'] = $order['can_use_integral'];
            } else {//否则就是 使用积分
                if ($member['integral'] > $order['can_use_integral']) {//账户余额大于可使用积分时
                    $member['integral'] -= $order['can_use_integral'];
                    $orders[$k]['use_integral'] = $order['use_integral'] = $order['can_use_integral'];
                    $this->save($order);//保存ORDER
                    $users->addIntegral($uid, -$order['can_use_integral'], '商城购物使用积分');
                } elseif ($member['integral'] > 0) { //账户余额小于积分时
                    $orders[$k]['use_integral'] = $order['use_integral'] = $member['integral'];
					
                    $this->save($order);//保存ORDER
                    $users->addIntegral($uid, -$member['integral'], '商城购物使用积分');//小于等于0 就不执行了
                    $member['integral'] = 0;
					
                }
            }
			
            $useint += $order['use_integral'];
            $fan += $order['mobile_fan'];
            $total += $order['total_price'];
			$coupon_price += $order['coupon_price'];
        }
		//估计又问题，后期用户提出来在修改吧
		$config = D('Setting')->fetchAll();//积分比例控制
		if($config['integral']['buy'] == 0){
			$useint_price = $useint;
		}else{
			$useint_price = $useint * $config['integral']['buy'];	
		}
		return $total - $fan - $useint_price - $coupon_price;
    }
    public function overOrder($order_id){
        //后台管理员可以直接确认2的
        $order = $this->find($order_id);
        if (empty($order)) {
            return false;
        }
        if ($order['status'] != 2 && $order['status'] != 3) {
            return false;
        }
        if ($this->save(array('status' => 8, 'order_id' => $order_id))) {
            $userobj = D('Users');
            $goods = D('Ordergoods')->where(array('order_id' => $order_id))->select();
            $shop = D('Shop')->find($order['shop_id']);
            if (!empty($goods)) {
                D('Ordergoods')->save(array('status' => 8), array('where' => array('order_id' => $order_id)));
                if ($order['is_daofu'] == 0) {
                    $ip = get_client_ip();
                    $info = '购物结算';
                    foreach ($goods as $val) {
                        $money = $val['js_price'];
                        if ($money > 0) {
                            D('Shopmoney')->add(array(
								'shop_id' => $order['shop_id'], 
								'city_id' => $shop['city_id'], 
								'area_id' => $shop['area_id'], 
								'money' => $money, 
								'create_time' => NOW_TIME, 
								'create_ip' => $ip, 
								'type' => 'goods', 
								'order_id' => $order_id, 
								'intro' => $info
							));
                            D('Users')->Money($shop['user_id'], $money, '商户商城订单资金结算：' . $order_id);
                            //写入金块
                        }
                    }
                    // 购物积分奖励给买的人，这个开关在后台
                    D('Users')->gouwu($order['user_id'], $order['total_price'], '购物积分奖励');
					//返还积分给商家用户
					$mall_order = D('Order')->find($order_id);
					if(!empty($mall_order['use_integral'])){
						$config = D('Setting')->fetchAll();
						if($config['integral']['mall_return_integral'] == 1){
							D('Users')->return_integral($shop['user_id'], $mall_order['use_integral'] , '商城用户积分兑换返还给商家');
						}
					}
					
                }
            }
            return true;
        }
        return false;
    }
	
	//后台退款跟商家退款逻辑封装
	public function implemented_refund($order_id){
		$order_id = (int) $order_id;
		$order = D('Order');
        $detail = $order->find($order_id);
		if ($detail['status'] != 4) {
             return false;
        }
		if (!empty($order_id)) {
			//返还余额
			$order->save(array('order_id' => $detail['order_id'], 'status' => 5)); //更改已退款状态
			$obj = D('Users');
			if ($detail['need_pay'] > 0) {
				$obj->addMoney($detail['user_id'], $detail['need_pay'], '商城退款，订单号：' . $detail['order_id']);
			}
			if ($detail['use_integral'] > 0) {
				$obj->addIntegral($detail['user_id'], $detail['use_integral'], '商城退款积分返还，订单号：' . $detail['order_id']);
			}
			$this->order_goods_status($order_id);//更高订单表状态
			$this->goods_num($order_id); //增加库存
			D('Sms') -> goods_refund_user($order_id);//推广成功短信通知用户
        }else{
			return false;	
	   }
	   return TRUE;
	}
	
	
		
   //后台退款跟商家退款更新购物表的状态
   public function order_goods_status($order_id) {
       $order_id = (int) $order_id;
       $order_goods = D('Ordergoods')->where(array('order_id' => $order_id))->select();
			foreach ($order_goods as $k => $v){
				D('Ordergoods')->where('order_id =' . $v['order_id'])->setField('status', 3);
        }
      return TRUE;
    }
	
  //后台退款跟商家退款更新退款库存
   public function goods_num($order_id) {
       $order_id = (int) $order_id;
       $ordergoods = D('Ordergoods')->where('order_id =' . $order_id)->select();
       foreach ($ordergoods as $k => $v) {
       	 D('Goods')->updateCount($v['goods_id'], 'num', $v['num']);
       }
      return TRUE;
    }
	
	
	

	
	//批量付款更新收货地址
    public function update_order_ids_addr_id_message($order_ids,$addr_id,$message) {
		$obj = D('Order');
        if (is_array($order_ids)) {
            $order_ids = join(',', $order_ids);//这里还是有一点点区别
            $order = $obj->where("order_id IN ({$order_ids})")->select();
            foreach ($order as $k => $v) {
               $obj->save(array('order_id' => $v['order_id'], 'addr_id' => $addr_id,'message'=>$message)); 
            }
        } else {
            $order_ids = (int) $order_ids;
            $order = $obj->where('order_id =' . $order_ids)->select();
            foreach ($order as $k => $v) {
                $obj->save(array('order_id' => $v['order_id'], 'addr_id' => $addr_id,'message'=>$message));    
            }
        }
        return TRUE;
    }



    public function money($bg_time, $end_time, $shop_id){
        $bg_time = (int) $bg_time;
        $end_time = (int) $end_time;
        $shop_id = (int) $shop_id;
        if (!empty($shop_id)) {
            $data = $this->query(" SELECT sum(total_price)/100 as price,FROM_UNIXTIME(create_time,'%m%d') as d from  " . $this->getTableName() . "   where status=8 AND create_time >= '{$bg_time}' AND create_time <= '{$end_time}' AND shop_id = '{$shop_id}'  group by  FROM_UNIXTIME(create_time,'%m%d')");
        } else {
            $data = $this->query(" SELECT sum(total_price)/100 as price,FROM_UNIXTIME(create_time,'%m%d') as d from  " . $this->getTableName() . "   where status=8 AND create_time >= '{$bg_time}' AND create_time <= '{$end_time}'  group by  FROM_UNIXTIME(create_time,'%m%d')");
        }
        $showdata = array();
        $days = array();
        for ($i = $bg_time; $i <= $end_time; $i += 86400) {
            $days[date('md', $i)] = '\'' . date('m月d日', $i) . '\'';
        }
        $price = array();
        foreach ($days as $k => $v) {
            $price[$k] = 0;
            foreach ($data as $val) {
                if ($val['d'] == $k) {
                    $price[$k] = $val['price'];
                }
            }
        }
        $showdata['d'] = join(',', $days);
        $showdata['price'] = join(',', $price);
        return $showdata;
    }
	
}