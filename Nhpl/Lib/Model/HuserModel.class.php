<?php
class HuserModel extends CommonModel{
    protected $pk = 'huser_id';
    protected $tableName = 'huser';
    protected $_integral_type = array('share' => '发帖分享', 'reply' => '回复帖子', 'mobile' => '手机认证', 'email' => '邮件认证');
	
	public function getError() {
        return $this->error;
    }
	
	
    public function checkInvite($uid, $money){
        //推广员功能
        $uid = (int) $uid;
        $money = (int) $money;
        if ($money <= 0) {
            return false;
        }
        $user = $this->find($uid);
        $invite_t = NOW_TIME - 30 * 86400;
        if (!empty($user['invite_id'])) {
            if ($invite_t < $user['reg_time']) {
                //30天推广有效果
                $user2 = $this->find($user['invite_id']);
                //邀请人
                $ranks = D('Userrank')->fetchAll();
                //查出所有的Userrank
                $integral = (int) ($money * $ranks[$user2['rank_id']]['rebate'] / 100);
                if ($money <= 0) {
                    return false;
                }
                return array('uid' => $user2['user_id'], 'integral' => $integral);
            }
        }
        return false;
    }
    public function getUserByAccount($account){
        $data = $this->find(array('where' => array('account' => $account)));
        return $this->_format($data);
    }
    public function getUserByMobile($mobile)
    {
        $data = $this->find(array('where' => array('mobile' => $mobile)));
        return $this->_format($data);
    }
    //邮件登录暂时不处理
    public function getUserByEmail($email)
    {
        $data = $this->find(array('where' => array('email' => $email)));
        return $this->_format($data);
    }
    public function getUserByUcId($uc_id)
    {
        $data = $this->find(array('where' => array('uc_id' => (int) $uc_id)));
        return $this->_format($data);
    }
    //声望不记录日志了
    public function prestige($user_id, $mdl)
    {
        static $CONFIG;
        if (empty($CONFIG)) {
            $CONFIG = D('Setting')->fetchAll();
        }
        $user = $this->find($user_id);
        if (!empty($user) && $CONFIG['prestige'][$mdl]) {
            $data = array('user_id' => $user_id, 'prestige' => $user['prestige'] + $CONFIG['prestige'][$mdl]);
            $userrank = D('Userrank')->fetchAll();
            foreach ($userrank as $val) {
                if ($val['prestige'] <= $data['prestige']) {
                    $data['rank_id'] = $val['rank_id'];
                }
            }
            return $this->save($data);
        }
        return false;
    }
    public function integral($user_id, $mdl)
    {
        static $CONFIG;
        if (empty($CONFIG)) {
            $CONFIG = D('Setting')->fetchAll();
        }
        if (!isset($this->_integral_type[$mdl])) {
            return false;
        }
        if ($CONFIG['integral'][$mdl]) {
            return $this->addIntegral($user_id, $CONFIG['integral'][$mdl], $this->_integral_type[$mdl]);
        }
        return false;
    }
    //购物返积分
    public function gouwu($user_id, $price, $intro)
    {
        static $CONFIG;
        if (empty($CONFIG)) {
            $CONFIG = D('Setting')->fetchAll();
        }
        if (empty($CONFIG['integral']['gouwu'])) {
            return false;
        }
        $price = (int) ($price / 100);
        $integral = (int) ($price * $CONFIG['integral']['gouwu']);
        if ($integral <= 0) {
            return false;
        }
        return $this->addIntegral($user_id, $integral, $intro);
    }
	
	 //积分兑换商品返还积分给商家中间层
    public function return_integral($user_id, $jifen, $intro){
        static $CONFIG;
        if (empty($CONFIG)) {
            $CONFIG = D('Setting')->fetchAll();
        }
        if (empty($CONFIG['integral']['return_integral'])) {
            return false;
        }
        $integral = intval(($jifen * $CONFIG['integral']['return_integral'])/100);
        if ($integral <= 0) {
            return false;
        }
        return $this->addIntegral($user_id, $integral, $intro);
    }
	
	
    public function addGold($user_id, $num, $intro = ''){
        if ($this->updateCount($user_id, 'gold', $num)) {
            return D('Usergoldlogs')->add(array('user_id' => $user_id, 'gold' => $num, 'intro' => $intro, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()));
        }
        return false;
    }
	
	//写入商户金块
	  public function Money($user_id, $num, $intro = ''){
        if ($this->updateCount($user_id, 'gold', $num)) {
            return D('Usergoldlogs')->add(array(
				'user_id' => $user_id, 
				'gold' => $num, 
				'intro' => $intro, 
				'create_time' => NOW_TIME, 
				'create_ip' => get_client_ip()
			));
        }
        return false;
    }
	//写入用户余额
    public function addMoney($user_id, $num, $intro = ''){
        if ($this->updateCount($user_id, 'money', $num)) {
            return D('Usermoneylogs')->add(array('user_id' => $user_id, 'money' => $num, 'intro' => $intro, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()));
        }
        return false;
    }
	
	
    public function addIntegral($user_id, $num, $intro = ''){
        if ($this->updateCount($user_id, 'integral', $num)) {
            return D('Userintegrallogs')->add(array('user_id' => $user_id, 'integral' => $num, 'intro' => $intro, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()));
        }
	
        return false;
    }
    public function CallDataForMat($items){
        //专门针对CALLDATA 标签处理的
        if (empty($items)) {
            return array();
        }
        $obj = D('Userrank');
        $rank_ids = array();
        foreach ($items as $k => $val) {
            $rank_ids[$val['rank_id']] = $val['rank_id'];
        }
        $userranks = $obj->itemsByIds($rank_ids);
        foreach ($items as $k => $val) {
            $val['rank'] = $userranks[$val['rank_id']];
            $items[$k] = $val;
        }
        return $items;
    }
	
	//检测积分设置合法性
	public function check_integral_buy($integral_buy){
		$config = D('Setting')->fetchAll();
		if($config['integral']['integral_exchange'] !=1){//没开启返回假
			return false;	
		}else{
			if($integral_buy  == 0 || $integral_buy  == 10 || $integral_buy  == 100){
				 return true;
			}else{
				return false;
			}	
		}
         return true;
    }
	//获取积分兑换设置比例
	public function obtain_integral_scale($integral_buy){
		if($integral_buy  == 0){
			$scale = 1;
			return $scale;
		}elseif($integral_buy  == 10){
			$scale = 10;
			return $scale;
		}elseif($integral_buy  == 100){
			$scale = 100;
			return $scale;
		}else{
			return false;
		}
       return false;
    }
	
	//检测积分兑换余额的合法性
	public function check_integral_exchange_legitimate($exchange,$scale){
		$config = D('Setting')->fetchAll();
		if($scale == 1){
			if ($exchange % 100 != 0) {
				$this->error = '积分必须为100的倍数';
				return false;
			}
		}elseif($scale == 10){
			if ($exchange % 10 != 0) {
				$this->error = '积分必须为10的倍数';
				return false;
			}
		}elseif($scale == 100){
			if ($exchange % 1 != 0) {
				$this->error = '积分必须为1的倍数，不支持小数点';
				return false;
			}
		}else{
			 $this->error = '网站后台配置有错误请注意检查';
			 return false;
		}
		if($exchange <= $config['integral']['integral_exchange_small']){
			$this->error = '输入的积分小于网站设置的最少积分数量'.$config['integral']['integral_exchange_small'];
			return false;
        }
		if($exchange >= $config['integral']['integral_exchange_big']){
			$this->error = '输入的积分太多了，最多输入积分数量：'.$config['integral']['integral_exchange_big'];
			return false;
        }
		return true;
    }
	
	//三级分销封装
    public function addProfit($user_id, $orderType = 0, $orderId, $num, $is_separate){
        return D('Userprofitlogs')->add(array(
			'user_id' => $user_id, 
			'money' => $num, 
			'order_id' => $orderId, 
			'order_type' => $orderType, 
			'create_time' => NOW_TIME, 
			'is_separate' => $is_separate
		));
    }
	
	//充值多少送多少
    public function Recharge_Full_Gvie_User_Money($user_id, $money){
		$CONFIG = D('Setting')->fetchAll();
        if (!empty($CONFIG['cash']['is_recharge'])) {
			$money = round($money/100,2);//先除以100再去对比
 			$give_money_array = $this->Check_Gvie_User_Money($money);
			if(!empty($give_money_array)){
				extract($give_money_array); 
				$give_money = $give_money;
				$intro = $intro;
				if($give_money > 0){
					return $this->addMoney($user_id, $give_money*100, $intro);
				}	
		    }
        }else{
			return true;//忽略报错
		}
    }
	
    //检测应该送多少钱
    public function Check_Gvie_User_Money($money){
		$CONFIG = D('Setting')->fetchAll();
		if (!empty($CONFIG['cash']['is_recharge']) && !empty($money)) {
			
		//正常模式，后台填写1,2,3，判断都有，就走这里
		if (!empty($CONFIG['cash']['recharge_full_1']) && !empty($CONFIG['cash']['recharge_full_2']) && !empty($CONFIG['cash']['recharge_full_3'])) {
			if(!empty($CONFIG['cash']['recharge_full_1']) && $money >= $CONFIG['cash']['recharge_full_1'] && $money < $CONFIG['cash']['recharge_full_2']){
				if(!empty($CONFIG['cash']['recharge_give_1'])){
					$give_money = $CONFIG['cash']['recharge_give_1'];
					$intro = '您单笔充值'.$money.'返现'.$CONFIG['cash']['recharge_give_1'].'元';
					return array('give_money' => $give_money, 'intro' =>$intro );
				}else{
					return false;
				}
			}elseif(!empty($CONFIG['cash']['recharge_full_2']) && $money >= $CONFIG['cash']['recharge_full_2'] && $money < $CONFIG['cash']['recharge_full_3']){
				if(!empty($CONFIG['cash']['recharge_give_2'])){
					$give_money = $CONFIG['cash']['recharge_give_2'];
					$intro = '您单笔充值'.$money.'返现'.$CONFIG['cash']['recharge_give_2'].'元';
					return array('give_money' => $give_money, 'intro' =>$intro );
				}else{
					return false;
				}
			}elseif(!empty($CONFIG['cash']['recharge_full_3']) && $money >= $CONFIG['cash']['recharge_full_3']){
				if(!empty($CONFIG['cash']['recharge_give_3'])){
					$give_money = $CONFIG['cash']['recharge_give_3'];
					$intro = '您单笔充值'.$money.'返现'.$CONFIG['cash']['recharge_give_3'].'元';
					return array('give_money' => $give_money, 'intro' =>$intro );
				}else{
					return false;
				}
			}
		}
			
		//1，2模式，后台填写1，2，只有1，2，后面的3是空走这里
		if (!empty($CONFIG['cash']['recharge_full_1']) && !empty($CONFIG['cash']['recharge_full_2']) && empty($CONFIG['cash']['recharge_full_3'])) {
			if(!empty($CONFIG['cash']['recharge_full_1']) && $money >= $CONFIG['cash']['recharge_full_1'] && $money < $CONFIG['cash']['recharge_full_2']){
				if(!empty($CONFIG['cash']['recharge_give_1'])){
					$give_money = $CONFIG['cash']['recharge_give_1'];
					$intro = '您单笔充值'.$money.'返现'.$CONFIG['cash']['recharge_give_1'].'元';
					return array('give_money' => $give_money, 'intro' =>$intro );
				}else{
					return false;
				}
			}elseif(!empty($CONFIG['cash']['recharge_full_2']) && $money >= $CONFIG['cash']['recharge_full_2']){
				if(!empty($CONFIG['cash']['recharge_give_2'])){
					$give_money = $CONFIG['cash']['recharge_give_2'];
					$intro = '您单笔充值'.$money.'返现'.$CONFIG['cash']['recharge_give_2'].'元';
					return array('give_money' => $give_money, 'intro' =>$intro );
				}else{
					return false;
				}
			}
		 }
		
		//1模式，后台填写1，只有1，后面的2并3都是空走这里
		if (!empty($CONFIG['cash']['recharge_full_1']) && empty($CONFIG['cash']['recharge_full_2']) && empty($CONFIG['cash']['recharge_full_3'])) {
			if(!empty($CONFIG['cash']['recharge_full_1']) && $money >= $CONFIG['cash']['recharge_full_1']){
				if(!empty($CONFIG['cash']['recharge_give_1'])){
					$give_money = $CONFIG['cash']['recharge_give_1'];
					$intro = '您单笔充值'.$money.'返现'.$CONFIG['cash']['recharge_give_1'].'元';
					return array('give_money' => $give_money, 'intro' =>$intro );
				}else{
					return false;
				}
			}
		 }
			
		}else{
			return false;
		}
		return false;
    }
}