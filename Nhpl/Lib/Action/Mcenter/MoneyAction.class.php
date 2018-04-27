<?php
class MoneyAction extends CommonAction
{
    public function index(){
        $this->assign('payment', D('Payment')->getPayments(true));
        $this->display();
    }
    public function moneypay(){
        $money = (int) ($this->_post('money') * 100);
        $code = $this->_post('code', 'htmlspecialchars');
        if ($money <= 0) {
            $this->error('请填写正确的充值金额！');
        }
        if ($money > 1000000) {
            $this->error('每次充值金额不能大于1万');
        }
        $payment = D('Payment')->checkPayment($code);
        if (empty($payment)) {
            $this->error('该支付方式不存在');
        }
        $logs = array(
			'user_id' => $this->uid, 
			'type' => 'money', 
			'code' => $code, 
			'order_id' => 0, 
			'need_pay' => $money, 
			'create_time' => NOW_TIME, 
			'create_ip' => get_client_ip()
		);
        $logs['log_id'] = D('Paymentlogs')->add($logs);
        $this->assign('button', D('Payment')->getCode($logs));
        $this->assign('money', $money);
        $this->assign('logs', $logs);
        $this->display();
    }
    public function recharge(){
        //代金券充值
        if ($this->isPost()) {
            $card_key = $this->_post('card_key', htmlspecialchars);
            if (!D('Lock')->lock($this->uid)) {
                $this->fengmiMsg('服务器繁忙，1分钟后再试');
            }
            if (empty($card_key)) {
                D('Lock')->unlock();
                $this->fengmiMsg('充值卡号不能为空');
            }
            if (!($detail = D('Rechargecard')->where(array('card_key' => $card_key))->find())) {
                D('Lock')->unlock();
                $this->fengmiMsg('该充值卡不存在');
            }
            if ($detail['is_used'] == 1) {
                D('Lock')->unlock();
                $this->fengmiMsg('该充值卡已经使用过了');
            }
            $member = D('Users')->find($this->uid);
            $member['money'] += $detail['value'];
            if (D('Users')->save(array('user_id' => $this->uid, 'money' => $member['money']))) {
                D('Usermoneylogs')->add(array(
					'user_id' => $this->uid, 
					'money' => +$detail['value'], 
					'create_time' => NOW_TIME, 
					'create_ip' => get_client_ip(), 
					'intro' => '代金券充值' . $detail['card_id']
				));
                $res = D('Rechargecard')->save(array('card_id' => $detail['card_id'], 'is_used' => 1));
                if (!empty($res)) {
                    D('Rechargecard')->save(array('card_id' => $detail['card_id'], 'user_id' => $this->uid, 'used_time' => NOW_TIME));
                }
                $this->fengmiMsg('充值成功！', U('money/recharge'));
            }
            D('Lock')->unlock();
        } else {
            $this->display();
        }
    }
	
	 //积分兑换余额
      public function exchange(){
        if($this->isPost()){
			$config = D('Setting')->fetchAll();
			$integral_buy = $config['integral']['buy'];
			//判断积分设置是否合法
			if (false == D('Users')->check_integral_buy($integral_buy)) {
				$this->fengmiMsg('网站后台积分设置不合法，请联系管理员');
			}
			
            $exchange = (int)$this->_post('exchange');
			if($exchange <=0){
                $this->fengmiMsg('要兑换的数量不能为空！');
            }
			$scale  = D('Users')->obtain_integral_scale($integral_buy);//获取积分比例便于同步
			
			//批量检测积分兑换余额批量代码封装
			if (!D('Users')->check_integral_exchange_legitimate($exchange,$scale)) {
				$this->fengmiMsg(D('Users')->getError());	  
			}
	
            if($this->member['integral'] < $exchange*$scale){
                $this->fengmiMsg('账户积分不足');
            }
			$actual_integral = $exchange*$scale;
			$money = $actual_integral - intval(($actual_integral*$config['integral']['integral_exchange_tax'])/100);
			if($money > 0){
				if(D('Users')->addMoney($this->uid,$money,'积分兑换现金')){
					D('Users')->addIntegral($this->uid,-$exchange,'扣除兑换余额使用积分');          
				} 
			}
            $this->fengmiMsg('您成功兑换余额'.round($money/100,2).'元',U('logs/moneylogs')); 
        }else{
             $this->display();
        }
    }
}