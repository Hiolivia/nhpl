<?php

class AppointModel extends CommonModel {

    protected $pk = 'appoint_id';
    protected $tableName = 'appoint';
    
	 public function add_appoint($appoint_id){
        $appoint_id = (int)$appoint_id;
        $data = $this->find($appoint_id);
        if(empty($data)){
            $data = array('appoint_id'=>$appoint_id);
            $this->add($data);
        }
        return $data;
    }
	
	public function appoint_buy($user_id,$appoint_id,$price,$order_id){
        $Appoint = D('Appoint')->find($appoint_id);//商品状态
		$shop = D('Shop')->find($Appoint['shop_id']);
		
		$user_intro = '购买家政'.$Appoint['title'].'订单号'.$order_id;
		$shop_intro = '用户购买家政结算：订单号'.$order_id;
		
		D('Users')->addMoney($user_id, -$price, $user_intro);//扣余额
		if ($price > 0) {
          D('Shopmoney')->add(array(
				'shop_id' => $shop['shop_id'], 
				'city_id' => $shop['city_id'], 
				'area_id' => $shop['area_id'], 
				'money' => $price, 
				'create_time' => NOW_TIME, 
				'create_ip' => $ip, 
				'type' => 'goods', 
				'order_id' => $order_id, 
				'intro' => $shop_intro
			));
          D('Users')->Money($shop['user_id'],$price,$shop_intro);//写入金块
         }
        return true;
    }
	
    public function getCfg() {
        return $this->svcCfg;
    }

}
