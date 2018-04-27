<?php
class SmsModel extends CommonModel{
    protected $pk = 'sms_id';
    protected $tableName = 'sms';
    protected $token = 'bao_sms';
	
	
    public function sendSms($code, $mobile, $data){
        $tmpl = $this->fetchAll();
		
        if (!empty($tmpl[$code]['is_open'])) {
            $content = $tmpl[$code]['sms_tmpl'];
            $config = D('Setting')->fetchAll();
            $data['sitename'] = $config['site']['sitename'];
            $data['tel'] = $config['site']['tel'];
		
            foreach ($data as $k => $val) {
                $val = str_replace('【', '', $val);
                $val = str_replace('】', '', $val);
                $content = str_replace('{' . $k . '}', $val, $content);
            }
			
			
            if (is_array($mobile)) {
                $mobile = join(',', $mobile);
            }
            if ($config['sms']['charset']) {
                $content = auto_charset($content, 'UTF8', 'gbk');
            }
            $local = array('mobile' => $mobile, 'content' => $content);
			$sms_id = $this->sms_bao_add($mobile,$content);
            $http = tmplToStr($config['sms']['url'], $local);
            $res = file_get_contents($http);
			D('Smsbao')->where(array('sms_id' => $sms_id))->save(array('status' => $res));
            if ($res == $config['sms']['code']) {
                return true;
            }
			
        }
        return false;
    }
    public function DySms($sign, $code, $mobile, $data){
        $config = D('Setting')->fetchAll();
        $dycode = D('Dayu')->where(array("dayu_local='{$code}'"))->find();
		
        if (!empty($dycode['is_open'])) {
			$sms_id = $this->sms_dayu_add($sign, $code, $mobile,$data,$dycode['dayu_note']);
            import('ORG.Util.Dayu');
            $obj = new AliSms($config['sms']['dykey'], $config['sms']['dysecret']);
            if ($obj->sign($sign)->data($data)->sms_id($sms_id)->code($dycode['dayu_tag'])->send($mobile)) {
                return true;
            }
        }
        return false;
    }
	public function sms_bao_add($mobile,$content){
		$sms_data = array();
		$sms_data['mobile'] = $mobile;
		$sms_data['content'] = $content;
		$sms_data['create_time'] = time();
		$sms_data['create_ip'] = get_client_ip();
		if ($sms_id = D('Smsbao')->add($sms_data)) {
            return $sms_id;
        }
		return true;
	}
	public function sms_dayu_add($sign, $code, $mobile,$data,$dayu_note){
		foreach ($data as $k => $val) {
			$content = str_replace('${' . $k . '}', $val, $dayu_note);
			$dayu_note = $content;
		}
		$sms_data = array();
		$sms_data['sign'] = $sign.'-'.time();
		$sms_data['code'] = $code;
		$sms_data['mobile'] = $mobile;
		$sms_data['content'] = $content;
		$sms_data['create_time'] = time();
		$sms_data['create_ip'] = get_client_ip();
		if ($sms_id = D('Dayusms')->add($sms_data)) {
            return $sms_id;
        }
		return true;
	}
    public function mallTZshop($order_id){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $order_id = array($order_id);
        }
        $config = D('Setting')->fetchAll();
        $orders = D('Order')->itemsByIds($order_id);
        $shop = array();
        foreach ($orders as $val) {
            $shop[$val['shop_id']] = $val['shop_id'];
        }
        $shops = D('Shop')->itemsByIds($shop);
        foreach ($shops as $val) {
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'sms_mall_tz_shop',$val['mobile'], array(
					'sitename' => $config['site']['sitename']
				));
            } else {
                $this->sendSms('sms_shop_mall', $val['mobile'], array());
            }
        }
        return true;
    }
	//用户下载优惠劵通知用户手机
	public function coupon_download_user($download_id,$uid){
		 $Coupondownload = D('Coupondownload')->find($download_id);
		 $Coupon = D('Coupon')->find($Coupondownload['coupon_id']);
		 $user = D('Users')->find($uid);
		 $config = D('Setting')->fetchAll();
		 //如果有手机号
		 if(!empty($user['mobile'])){
			if($config['sms']['dxapi'] == 'dy'){
                D('Sms')->DySms($config['site']['sitename'], 'coupon_download_user',$user['mobile'], array(
                    'coupon_title' => $Coupon['title'],
                    'code' => $Coupondownload['code'],
                    'expire_date' => $Coupon['expire_date']
                ));
            }else{
                D('Sms')->sendSms('coupon_download_user',$user['mobile'], array(
                    'coupon_title' => $Coupon['title'],
                    'code' => $Coupondownload['code'],
                    'expire_date' => $Coupon['expire_date'],
                ));
            }
		 }else{
			return false; 
		}
		 return true;
	}	
		
	
	
	
	
	
	
	
    public function eleTZshop($order_id){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $order = D('Eleorder')->find($order_id);
            $config = D('Setting')->fetchAll();
            $shop = D('Shop')->find($order['shop_id']);
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'sms_ele_tz_shop',$shop['mobile'], array(
					'sitename' => $config['site']['sitename'], 
					'sitename' => $config['site']['sitename']
				));
            } else {
                $this->sendSms('sms_shop_ele', $shop['mobile'], array());
            }
        }
        return true;
    }
	
	public function breaksTZshop($order_id){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $order = D('Breaksorder')->find($order_id);
            $config = D('Setting')->fetchAll();
            $shop = D('Shop')->find($order['shop_id']);
			$users = D('Users')->find($order['user_id']);
			if(!empty($users['nickname'])){
				$user_name = $users['nickname'];
			}else{
				$user_name = $users['account'];
			}
			if(!empty($shop['mobile'])){
				if ($config['sms']['dxapi'] == 'dy') {
					$this->DySms($config['site']['sitename'], 'sms_breaks_tz_shop',$shop['mobile'], array(
						'shop_name' => $shop['shop_name'], //商家名字
						'user_name' => $user_name, //会员名字
						'amount' => $order['amount'], //买单金额
						'money' => $order['need_pay']//实际付款
					));
				} else {
					$this->sendSms('sms_breaks_tz_shop', $shop['mobile'], array(
						'shop_name' => $shop['shop_name'], //商家名字
						'user_name' => $user_name, //会员名字
						'amount' => $order['amount'], //买单金额
						'money' => $order['need_pay']//实际付款
					));
				}
			}
        }
        return true;
    }
	
	
	public function breaksTZuser($order_id){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $order = D('Breaksorder')->find($order_id);
            $config = D('Setting')->fetchAll();
            $users = D('Users')->find($order['user_id']);
			if(!empty($users['nickname'])){
				$user_name = $users['nickname'];
			}else{
				$user_name = $users['account'];
			}
			$shop = D('Shop')->find($order['shop_id']);
			$t = time();
            $date = date('Y-m-d H:i:s ', $t);
			if(!empty($users['mobile'])){
				if ($config['sms']['dxapi'] == 'dy') {
					$this->DySms($config['site']['sitename'], 'sms_breaks_tz_user',$users['mobile'], array(
						'user_name' => $user_name, //会员名字
						'shop_name' => $shop['shop_name'], //商家名字
						'money' => $order['need_pay'], //实付金额
						'data' => $date, //买单时间
					));
				} else {
					$this->sendSms('sms_breaks_tz_user', $user['mobile'], array(
						'user_name' => $user_name, //会员名字
						'shop_name' => $shop['shop_name'], //商家名字
						'money' => $order['need_pay'], //实付金额
						'data' => $date, //买单时间
					));
				}
			}
        }
        return true;
    }
	
	//商家抢购劵验证成功后发送消息到用户手机
    public function tuan_TZ_user($code_id){
        if (is_numeric($code_id) && ($code_id = (int) $code_id)) {
            $tuancode = D('Tuancode')->find($code_id);
            $config = D('Setting')->fetchAll();
            $user = D('Users')->find($tuancode['user_id']);
            //用户手机号
            $tuan = D('Tuan')->find($tuancode['tuan_id']);
            $t = time();
            $date = date('Y-m-d H:i:s ', $t);
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'],'tuan_TZ_user',$user['mobile'], array(
					'name' => $tuan['title'], 
					'data' => $date, 
					'tel' => $config['site']['tel']
				));
            } else {
                $this->sendSms('tuan_TZ_user',$user['mobile'], array());
            }
        }
        return true;
    }
	//发送团购劵到用户手机
	 public function sms_tuan_user($uid,$order_id){
		$user = D('Users')->find($uid);
		$config = D('Setting')->fetchAll();
		$order = D('Tuancode')->where(array('order_id'=>$order_id))->select();
		$tuan = D('Tuan')->find($order['tuan_id']);
		foreach($order as $v){
			$code[] =  $v['code'];
		}
		$codestr = join(',', $code);
        //发送团购劵
        if ($config['sms']['dxapi'] == 'dy') {
           D('Sms')->DySms($config['site']['sitename'], 'sms_tuan_user',$user['mobile'], array(
				'code' => $codestr, 
				'user' => $user['nickname'], 
				'shop_name' => $tuan['title']
			));
        }else{
           D('Sms')->sendSms('sms_tuan', $user['mobile'], array(
				'code' => $codestr, 
				'nickname' => $user['nickname'], 
				'tuan' => $tuan['title']
			));
        }
		return true;
	}			
				
    public function dingTZshop($order_id)
    {
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $config = D('Setting')->fetchAll();
            $order = D('Shopdingorder')->find($order_id);
            $shop = D('Shop')->find($order['shop_id']);
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'sms_ele_shop',$shop['mobile'], array('sitename' => $config['site']['sitename']));
            } else {
                $this->sendSms('sms_shop_ele', $shop['mobile'], array());
            }
        }
        return true;
    }
    public function tuanTZshop($shop_id)
    {
        $shop_id = (int) $shop_id;
        $shop = D('Shop')->find($shop_id);
        $config = D('Setting')->fetchAll();
        if ($config['sms']['dxapi'] == 'dy') {
            $this->DySms($config['site']['sitename'], 'sms_tuan_tz_shop',$shop['mobile'], array('sitename' => $config['site']['sitename']));
        } else {
            $this->sendSms('sms_shop_tuan', $shop['mobile'], array());
        }
        return true;
    }
	
		
	//预订通知会员
	public function sms_booking_user($order_id){
		    $order = D('Bookingorder')->find($logs['order_id']);
        	$member = D('Users')->find($order['user_id']);
            $booking = D('Booking')->find($order['shop_id']);//这里是预订里面填写的手机
			$config = D('Setting')->fetchAll();
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'sms_booking_user',$member['mobile'], array(
					    'booking_name' => $booking['shop_name'],//预订名字
					));
            } else {
                $this->sendSms('sms_booking_user', $member['mobile'], array(
					   'booking_name' => $booking['shop_name'],//预订名字
				));
            }
        return true;
    }
	//预订通知商家
	public function sms_booking_shop($order_id){
		    $order = D('Bookingorder')->find($logs['order_id']);
        	$member = D('Users')->find($order['user_id']);
            $booking = D('Booking')->find($order['shop_id']);//这里是预订里面填写的手机
			$config = D('Setting')->fetchAll();
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'sms_booking_shop',$shops['mobile'], array(
					    'booking_name' => $booking['shop_name'],//预订名字
					));
            } else {
                $this->sendSms('sms_booking_shop', $shops['mobile'], array(
					    'booking_name' => $booking['shop_name'],//预订名字
				));
            }
        return true;
    }
	
	
	//众筹通知用户
	public function sms_crowd_user($order_id){
        	$order = D('Crowdorder')->find($logs['order_id']);
            $Crowd = D('Crowd')->find($order['goods_id']);
			$users = D('Users')->find($order['user_id']);
			$config = D('Setting')->fetchAll();
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'sms_crowd_user',$users['mobile'], array(
						'user_name' => $users['nickname'],//买家姓名
					    'title' => $Crowd['title'],//众筹名字
					));
            } else {
                $this->sendSms('sms_crowd_user', $users['mobile'], array(
					    'user_name' => $users['nickname'],//买家姓名
					    'title' => $Crowd['title'],//众筹名字
				));
            }
        return true;
    }
	
	//众筹通知发起人
	public function sms_crowd_uid($order_id){
        	$order = D('Crowdorder')->find($logs['order_id']);
            $Crowd = D('Crowd')->find($order['goods_id']);
			$users = D('Users')->find($order['uid']);
			$config = D('Setting')->fetchAll();
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'sms_crowd_uid',$users['mobile'], array(
						'user_name' => $users['nickname'],//发起人姓名
					    'title' => $Crowd['title'],//众筹名字
					));
            } else {
                $this->sendSms('sms_crowd_uid', $users['mobile'], array(
					    'user_name' => $users['nickname'],//发起人姓名
					    'title' => $Crowd['title'],//众筹名字
				));
            }
        return true;
    }
	
	//商城退款短信通知
	public function goods_refund_user($order_id){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $order = D('Order')->find($order_id);
            $config = D('Setting')->fetchAll();
            $user = D('Users')->find($order['user_id']);
			$t = time();
            $date = date('Y-m-d H:i:s ', $t);
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'goods_refund_user',$user['mobile'], array(
					'need_pay' => round($order['need_pay']/100,2),//退款金额
					'order_id' => $order['order_id'],//订单ID
				));
            } else {
                $this->sendSms('goods_refund_user', $user['mobile'], array(
					'need_pay' => round($order['need_pay']/100,2),//退款金额
					'order_id' => $order['order_id'],//订单ID
				));
            }
        }
        return true;
    }
	
	//外卖退款短信通知用户
	public function eleorder_refund_user($order_id){
        if (is_numeric($order_id) && ($order_id = (int) $order_id)) {
            $ele_order = D('Eleorder')->find($order_id);
            $config = D('Setting')->fetchAll();
            $user = D('Users')->find($ele_order['user_id']);
			$t = time();
            $date = date('Y-m-d H:i:s ', $t);
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'eleorder_refund_user',$user['mobile'], array(
					'need_pay' => round($ele_order['need_pay']/100,2),//退款金额
					'order_id' => $order_id
				));
            } else {
                $this->sendSms('eleorder_refund_user', $user['mobile'], array(
					'need_pay' => round($ele_order['need_pay']/100,2),//退款金额
					'order_id' => $order_id
				));
            }
        }
        return true;
    }
	
	//抢购劵退款短信通知
	public function tuancode_refund_user($code_id){
        	$code_id = (int) $code_id;
            $tuancode = D('Tuancode')->find($code_id);
            $config = D('Setting')->fetchAll();
            $user = D('Users')->find($Tuancode['user_id']);
            if ($config['sms']['dxapi'] == 'dy') {
                $this->DySms($config['site']['sitename'], 'tuancode_refund_user',$user['mobile'], array(
					'real_money' => round($tuancode['real_money']/100,2),//退款金额
					'order_id' => $code_id,//订单ID
				));
            } else {
                $this->sendSms('tuancode_refund_user', $user['mobile'], array(
					'real_money' => round($tuancode['real_money']/100,2),//退款金额
					'order_id' => $code_id,//订单ID
				));
            }
        return true;
    }
	
	
    public function fetchAll()
    {
        $cache = cache(array('type' => 'File', 'expire' => $this->cacheTime));
        if (!($data = $cache->get($this->token))) {
            $result = $this->order($this->orderby)->select();
            $data = array();
            foreach ($result as $row) {
                $data[$row['sms_key']] = $row;
            }
            $cache->set($this->token, $data);
        }
        return $data;
    }
}