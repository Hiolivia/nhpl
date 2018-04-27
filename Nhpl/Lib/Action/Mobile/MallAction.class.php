<?php
class MallAction extends CommonAction{
    public function _initialize(){
        parent::_initialize();
        if ($this->_CONFIG['operation']['mall'] == 0) {
            $this->error('此功能已关闭');
            die;
        }
        $goods = cookie('goods');
        $this->assign('cartnum', (int) array_sum($goods));
        //统计商城分类数量代码开始
        $cat = (int) $this->_param('cat');
        $Goods = D('Goods');
        $goodscates = D('Goodscate')->fetchAll();
        foreach ($goodscates as $key => $v) {
            if ($v['cate_id']) {
                $catids = D('Goodscate')->getChildren($v['cate_id']);
                if (!empty($catids)) {
                    $count = $Goods->where(array('cate_id' => array('IN', $catids), 'closed' => 0, 'audit' => 1,'city_id'=>$this->city_id))->count();
                } else {
                    $count = $Goods->where(array('cate_id' => $cat, 'closed' => 0, 'audit' => 1,'city_id'=>$this->city_id))->count();
                }
            }
            $goodscates[$key]['count'] = $count;
        }
        $this->assign('goodscates', $goodscates);
    }
    public function index(){
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        $cat = (int) $this->_param('cat');
        $area = (int) $this->_param('area');
        $business = (int) $this->_param('business');
        $cate_id = (int) $this->_param('cate_id');
        $order = (int) $this->_param('order');
        $this->assign('area', $area);
        $this->assign('business', $business);
        $this->assign('cate_id', $cate_id);
        $this->assign('order', $order);
        $this->assign('cat', $cat);
        $this->assign('nextpage', LinkTo('mall/loaddata', array('cat' => $cat, 'order' => $order, 'area' => $area, 'business' => $business, 'cate_id' => $cate_id, 'keyword' => $keyword, 'p' => '0000')));
        $this->display();
    }
    public function main()
    {
        $cate_id = I('cate_id', '', 'trim,intval');
        $gc = D('GoodsCate');
        if ($cate_id) {
            $map['cate_id'] = array('eq', $cate_id);
            $gc_name = $gc->where('cate_id =' . $cate_id)->getField('cate_name');
            $this->assign('gc_name', $gc_name);
        }
        $this->assign('cate_id', $cate_id);
        $where = array();
        $where['cate_id'] = array('in', '1,14,2,6,8,21,25');
        $rgc = $gc->where($where)->select();
        $all_gc = $gc->where('parent_id = 0')->select();
        $this->assign('all_gc', $all_gc);
        $this->assign('rgc', $rgc);
        $map['city_id'] = $this->city_id;
        $map['audit'] = 1;
        $map['closed'] = 0;
        $map['end_date'] = array('egt', TODAY);
        $order = (int) $this->_param('order');
        switch ($order) {
            case 2:
                $orderby = array('sold_num' => 'desc');
                break;
            case 3:
                $orderby = array('goods_id' => 'desc');
                break;
            default:
                $orderby = array('mall_price' => 'asc', 'orderby' => 'asc');
                break;
        }
        $this->assign('order', $order);
        $list = D('Goods')->order($orderby)->where($map)->limit(0, 10)->select();
        $this->assign('list', $list);
        $this->display();
    }
    public function cate()
    {
        if (!($cate_id = (int) $this->_param('cat'))) {
            $this->error('没有该分类！');
        }
        $this->assign('cat', $cate_id);
        $this->display();
        // 输出模板
    }
    public function loaddata()
    {
        $Goods = D('Goods');
        import('ORG.Util.Page');
        // 导入分页类
        $area = (int) $this->_param('area');
        $order = (int) $this->_param('order');
        $business = (int) $this->_param('business');
        if ($area) {
            $map['area_id'] = $area;
        }
        $cate_id = (int) $this->_param('cate_id');
        if ($cate_id) {
            $map['cate_id'] = $cate_id;
        }
        $map['audit'] = 1;
        $map['closed'] = 0;
        $map['end_date'] = array('egt', TODAY);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
        }
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $catids = D('Goodscate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
        }
        $map['city_id'] = $this->city_id;
        $count = $Goods->where($map)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        if ($order == '1') {
            $order_arr = 'create_time desc';
        } elseif ($order == '2') {
            $order_arr = 'sold_num desc';
        } else {
            $order_arr = 'orderby desc';
        }
        $list = $Goods->where($map)->order($order_arr)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $val['end_time'] = strtotime($val['end_date']) - NOW_TIME + 86400;
            $list[$k] = $val;
        }
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
    public function buy($goods_id){
        $goods_id = (int) $goods_id;
        if (empty($goods_id)) {
            $this->error('请选择产品');
        }
        if (!($detail = D('Goods')->find($goods_id))) {
            $this->error('改商品不存在');
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->error('该商品不存在');
        }
        if ($detail['end_date'] < TODAY) {
            $this->error('该商品已经过期，暂时不能购买');
        }
        $goods = cookie('goods');
        if (isset($goods[$goods_id])) {
            $goods[$goods_id] = $goods[$goods_id] + 1;
        } else {
            $goods[$goods_id] = 1;
        }
        cookie('goods', $goods);
        header('Location:' . U('mall/cart'));
        die;
    }
    public function cartadd($goods_id){
        $shop_id = (int) $this->_param('shop_id');
        $goods_id = (int) $goods_id;
        if (empty($goods_id)) {
            die('请选择产品');
        }
        if (!($detail = D('Goods')->find($goods_id))) {
            die('该商品不存在');
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            die('该商品不存在');
        }
        if ($detail['end_date'] < TODAY) {
            die('该商品已经过期，暂时不能购买');
        }
        $goods = cookie('goods');
        if (isset($goods[$goods_id])) {
            $goods[$goods_id] = $goods[$goods_id] + 1;
        } else {
            $goods[$goods_id] = 1;
        }
        cookie('goods', $goods);
        die('0');
    }
    public function cartadd2(){
        if (IS_AJAX) {
            $shop_id = (int) $_POST['shop_id'];
            $goods_id = (int) $_POST['goods_id'];
            if (empty($goods_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '请选择商品'));
            }
            if (!($detail = D('Goods')->find($goods_id))) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品不存在'));
            }
            if ($detail['closed'] != 0 || $detail['audit'] != 1) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品不存在'));
            }
            if ($detail['end_date'] < TODAY) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该商品已经过期，暂时不能购买'));
            }
            if ($detail['num'] <= 0) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '亲！没有库存了！'));
            }
            $goods = cookie('goods');
            if (isset($goods[$goods_id])) {
                $goods[$goods_id] = $goods[$goods_id] + 1;
            } else {
                $goods[$goods_id] = 1;
            }
            cookie('goods', $goods);
            $this->ajaxReturn(array('status' => 'success', 'msg' => '加入购物车成功'));
        }
    }
    public function cart(){
        $goods = cookie('goods');
        $back = end($goods);
        $back = key($goods);
        $this->assign('back', $back);
        if (empty($goods)) {
            $this->error('亲还没有选购产品呢!', U('mall/index'));
        }
        $goods_ids = array_keys($goods);
        $cart_goods = D('Goods')->itemsByIds($goods_ids);
        $shop_ids = array();
        foreach ($cart_goods as $k => $val) {
            $cart_goods[$k]['buy_num'] = $goods[$k];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('cart_shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('cart_goods', $cart_goods);
        $this->display();
    }
    public function detail($goods_id) {
        $goods_id = (int) $goods_id;
        if (empty($goods_id)) {
            $this->error('商品不存在');
        }
        if (!($detail = D('Goods')->find($goods_id))) {
            $this->error('商品不存在');
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->error('商品不存在');
        }
        $shop_id = $detail['shop_id'];

        $recom = D('Goods')->where(array('shop_id' => $shop_id,'audit'=>1,'closed'=>1,'goods_id' => array('neq', $goods_id),'end_date'=> array('egt', TODAY)))->select();
        $record = D('Usersgoods');
        $insert = $record->getRecord($this->uid, $goods_id);
        $this->assign('recom', $recom);
        $this->assign('detail', $detail);
        $this->assign('shop', D('Shop')->find($shop_id));

        $pingnum = D('Goodsdianping')->where(array('goods_id' => $goods_id, 'show_date' => array('ELT', TODAY)))->count();
        $this->assign('pingnum', $pingnum);

        $score = (int) D('Goodsdianping')->where(array('goods_id' => $goods_id))->avg('score');
        if ($score == 0) {
            $score = 5;
        }
        $this->assign('score', $score);
		if(($detail['is_vs1'] || $detail['is_vs2'] || $detail['is_vs3'] || $detail['is_vs4'] || $detail['is_vs5'] || $detail['is_vs6']) ==1 ){
			 $this->assign('is_vs', $is_vs = 1);
		}else{
			$this->assign('is_vs', $is_vs = 0);
		}

        $this->assign('pics', D('Goodsphoto')->getPics($detail['goods_id']));
        $this->display();
    }
    public function cartdel()
    {
        $goods_id = (int) $this->_get('goods_id');
        $goods = cookie('goods');
        if (isset($goods[$goods_id])) {
            unset($goods[$goods_id]);
            cookie('goods', $goods);
        }
        die('0');
    }
    public function cartdel2()
    {
        $goods_id = (int) $this->_get('goods_id');
        $goods = cookie('goods');
        if (isset($goods[$goods_id])) {
            unset($goods[$goods_id]);
            cookie('goods', $goods);
        }
        header('Location:' . U('mall/cart'));
    }
    public function neworder()
    {
        $goods = $this->_get('goods');
        $goods = explode(',', $goods);
        if (empty($goods)) {
            $this->error('亲购买点吧');
        }
        $datas = array();
        foreach ($goods as $val) {
            $good = explode('-', $val);
            $good[1] = (int) $good[1];
            if (empty($good[0]) || empty($good[1])) {
                $this->error('亲购买点吧');
            }
            if ($good[1] > 99 || $good[1] < 0) {
                $this->error('本店不支持批发');
            }
            $datas[$good[0]] = $good[1];
        }
        cookie('goods', $datas);
        header('Location:' . U('mall/cart'));
        //有问题
        die;
    }
    public function order() {
        if (empty($this->uid)) {
            $this->fengmiMsg('请先登陆', U('passport/login'));
        }
        $user_integral = D("users")->field('integral')->find($this->uid);
        $num = $this->_post('num', false);
        $goods_ids = array();
        foreach ($num as $k => $val) {
            $val = (int) $val;
            if (empty($val)) {
                unset($num[$k]);
            } elseif ($val < 1 || $val > 99) {
                unset($num[$k]);
            } else {
                $goods_ids[$k] = (int) $k;
            }
        }
        if (empty($goods_ids)) {
            $this->fengmiMsg('很抱歉请填写正确的购买数量');
        }
        $goods = D('Goods')->itemsByIds($goods_ids);
        foreach ($goods as $key => $val) {
            if ($val['closed'] != 0 || $val['audit'] != 1 || $val['end_date'] < TODAY) {
                unset($goods[$key]);
            }
        }
        if (empty($goods)) {
            $this->fengmiMsg('很抱歉，您提交的产品暂时不能购买！');
        }
		if ($val['num'] < $num[$val['goods_id']]) {
            $this->fengmiMsg('亲！商品<' . $val['title'] . '>库存不够了,只剩' . $val['num'] . '件了！');
        }
        $tprice = 0;
        $all_integral = $total_mobile = 0;
        $ip = get_client_ip();
        $total_canuserintegral = $ordergoods = $total_price = array();
        foreach ($goods as $val) {
			if(!empty($this->member['is_agent'])){
			   $price = $val['is_agent_price'] * $num[$val['goods_id']];
			}else{
			   $price = $val['mall_price'] * $num[$val['goods_id']];
			}
            $js_price = $val['settlement_price'] * $num[$val['goods_id']];
            $mobile_fan = $val['mobile_fan'] * $num[$val['goods_id']];//每个商品的手机减少的钱
            $canuserintegral = $val['use_integral'] * $num[$val['goods_id']];//可使用积分=单个可使用*商品数量 每个产品的
            $m_price = $price - $mobile_fan;
            $tprice += $m_price;
            $total_mobile += $mobile_fan;
            $all_integral += $canuserintegral;
            //所有的订单的积分总数 用于后面判断 如果用户的积分超过这个数目 才减少
			
			if(!empty($this->member['is_agent'])){
			   $order_goods_price = $val['mall_price'];
			}else{
			   $order_goods_price = $val['is_agent_price'];
			}
			
            $ordergoods[$val['shop_id']][] = array(
				'goods_id' => $val['goods_id'], 
				'shop_id' => $val['shop_id'], 
				'num' => $num[$val['goods_id']], 
				'price' => $order_goods_price, 
				'total_price' => $price, 
				'mobile_fan' => $mobile_fan, 
				'is_mobile' => 1, 
				'js_price' => $js_price, 
				'create_time' => NOW_TIME, 
				'create_ip' => $ip
			);
            $total_canuserintegral[$val['shop_id']] += $canuserintegral; //不同商家的每个订单的 总共 可以使用的积分
            $total_price[$val['shop_id']] += $price; //不同商家的总价格
            $mm_price[$val['shop_id']] += $mobile_fan;  //不同商家的  总的 手机下单减少 的价格
          
        }
        //总订单
        $order = array('user_id' => $this->uid, 'create_time' => NOW_TIME, 'create_ip' => $ip, 'is_mobile' => 1);
        $tui = cookie('tui');
        if (!empty($tui)) {
            //推广部分
            $tui = explode('_', $tui);
            $tuiguang = array('uid' => (int) $tui[0], 'goods_id' => (int) $tui[1]);
        }
        $order_ids = array();
        foreach ($ordergoods as $k => $val) {
            $order['shop_id'] = $k;
            $order['total_price'] = $total_price[$k];
            $order['mobile_fan'] = $mm_price[$k]; //手机下单减少的价钱 不同商家是不同的订单的
            $order['can_use_integral'] = $total_canuserintegral[$k];//每个订单可以使用的积分的数量
            $shop = D('Shop')->find($k);
            $order['is_shop'] = (int) $shop['is_pei']; //是否由商家自己配送
            if ($order_id = D('Order')->add($order)) {
                //推广ID 赋值了
                $order_ids[] = $order_id;
                foreach ($val as $k1 => $val1) {
                    $val1['order_id'] = $order_id;
                    if (!empty($tuiguang)) {
                        if ($tuiguang['goods_id'] == $val1['goods_id']) {
                            $val1['tui_uid'] = $tuiguang['uid'];
                        }
                    }
                    D('Ordergoods')->add($val1);
                }
            }
        }
        cookie('goods', null);
		cookie('mall', null);
        if (count($order_ids) > 1) {
            $need_pay = D('Order')->useIntegral($this->uid, $order_ids);
            $logs = array(
				'type' => 'goods', 
				'user_id' => $this->uid, 
				'order_id' => 0, 
				'order_ids' => join(',', $order_ids), 
				'code' => '', 
				'need_pay' => $need_pay, 
				'create_time' => NOW_TIME, 
				'create_ip' => get_client_ip(), 
				'is_paid' => 0
			);
            $logs['log_id'] = D('Paymentlogs')->add($logs);
            $this->fengmiMsg('合并下单成功，接下来选择支付方式和配送地址！', U('mall/paycode', array('log_id' => $logs['log_id'])));
        } else {
            $this->fengmiMsg('下单成功，接下来选择支付方式和配送地址！', U('mall/pay', array('order_id' => $order_id)));
        }
        die;
    }
    public function paycode(){
        $log_id = (int) $this->_get('log_id');
        if (empty($log_id)) {
            $this->error('没有有效支付记录！');
        }
        if (!($detail = D('Paymentlogs')->find($log_id))) {
            $this->error('没有有效的支付记录！');
        }
        if ($detail['is_paid'] != 0 || empty($detail['order_ids']) || !empty($detail['order_id']) || empty($detail['need_pay'])) {
            $this->error('没有有效的支付记录！');
        }
        $order_ids = explode(',', $detail['order_ids']);
        $ordergood = D('Ordergoods')->where(array('order_id' => array('IN', $order_ids)))->select();
        $goods_id = $shop_ids = array();
        foreach ($ordergood as $k => $val) {
            $goods_id[$val['goods_id']] = $val['goods_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('goods', D('Goods')->itemsByIds($goods_id));
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('ordergoods', $ordergood);
		
		$useraddr = D('Useraddr')->get_user_addr_is_default($this->uid);//获取用户默认收货地址
		$this->assign('useraddr', $useraddr);
	
        $this->assign('payment', D('Payment')->getPayments(true));
        $this->assign('logs', $detail);
        $this->display();
    }
    public function pay(){
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
            die;
        }
        $this->check_mobile();
        cookie('goods', null);
		cookie('mall', null);
        //销毁cookie再一次，不过没用啊
        $order_id = (int) $this->_get('order_id');
        $order = D('Order')->find($order_id);
        if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
            $this->error('该订单不存在');
            die;
        }
        $ordergood = D('Ordergoods')->where(array('order_id' => $order_id))->select();
        $goods_id = $shop_ids = array();
        foreach ($ordergood as $k => $val) {
            $goods_id[$val['goods_id']] = $val['goods_id'];
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('goods', D('Goods')->itemsByIds($goods_id));
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('ordergoods', $ordergood);
        
		$useraddr = D('Useraddr')->get_user_addr_is_default($this->uid);//获取用户默认收货地址
		$this->assign('useraddr', $useraddr);
		
		if(!empty($order['download_id'])){
			$this->assign('download_id', $order['download_id']);
		}else{
			$this->assign('coupon', $coupon = D('Coupon')->Obtain_Coupon($order_id,$this->uid));
		}		
        $this->assign('citys', D('City')->fetchAll());
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('order', $order);
        $this->assign('payment', D('Payment')->getPayments(true));
        $this->display();
    }
    public function paycode2(){
        //这里是因为原来的是按订单付，这里是合并付款逻辑部分
        $log_id = (int) $this->_get('log_id');
        if (empty($log_id)) {
            $this->fengmiMsg('没有有效支付记录！');
        }
        if (!($detail = D('Paymentlogs')->find($log_id))) {
            $this->fengmiMsg('没有有效的支付记录！');
        }
        if ($detail['is_paid'] != 0 || empty($detail['order_ids']) || !empty($detail['order_id']) || empty($detail['need_pay'])) {
            $this->fengmiMsg('没有有效的支付记录！');
        }
        $order_ids = explode(',', $detail['order_ids']);
        $addr_id = (int) $this->_post('addr_id');
        if (empty($addr_id)) {
            $this->fengmiMsg('请选择一个要配送的地址！');
        }
		$message = $this->_param('message', 'htmlspecialchars');
		D('Order')->update_order_ids_addr_id_message($order_ids,$addr_id,$message);//批量更新收货更新跟留言
	
		if (false == D('Goods')->check_goods_id_mum($order_ids)) {//批量检测库存
			$this->fengmiMsg(D('Goods')->getError(), U('mall/index'));	 
		}
		
		//合并付款暂时无法使用优惠劵
        D('Order')->where(array('order_id' => array('IN', $order_ids)))->save(array('addr_id' => $addr_id));
        /**********************蜂蜜 修复合并付款的时候的系列订单错误问题*****************************/
        $orders = D('order')->where(array('order_id' => array('IN', $order_ids)))->select();
        foreach ($orders as $k => $val) {
            $need_pay[$val[order_id]] = $val['total_price'] - $val['mobile_fan'] - $val['use_integral'];
            D('Order')->where(array('order_id' => $val['order_id']))->save(array('need_pay' => $need_pay[$val[order_id]]));
        }
        /*****************************************************/
        if (!($code = $this->_post('code'))) {
            $this->fengmiMsg('请选择支付方式！');
        }
        if ($code == 'wait') {
            //如果是货到付款
            D('Order')->save(array('is_daofu' => 1), array('where' => array('order_id' => array('IN', $order_ids))));
            D('Ordergoods')->save(array('is_daofu' => 1), array('where' => array('order_id' => array('IN', $order_ids))));
            D('Payment')->mallSold($order_ids);
            D('Payment')->mallPeisong(array($order_ids), 1);
			D('Sms')->mallTZshop($order_ids);
            $this->fengmiMsg('恭喜您下单成功！', U('mcenter/goods/index'));
        } else {
            $payment = D('Payment')->checkPayment($code);
            if (empty($payment)) {
                $this->fengmiMsg('该支付方式不存在');
            }
            $detail['code'] = $code;
            D('Paymentlogs')->save($detail);
            $this->fengmiMsg('订单设置完成，即将进入付款。', U('mall/combine', array('log_id' => $detail['log_id'])));
        }
    }
    public function combine(){
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
            die;
        }
        $log_id = (int) $this->_get('log_id');
        if (!($detail = D('Paymentlogs')->find($log_id))) {
            $this->error('没有有效的支付记录！');
        }
    
        if ($detail['is_paid'] != 0 || empty($detail['order_ids']) || !empty($detail['order_id']) || empty($detail['need_pay'])) {
            $this->error('没有有效的支付记录！');
        }
        $this->assign('button', D('Payment')->getCode($detail));
        $this->assign('logs', $detail);
        $this->display();
    }
	//直接付款
    public function pay2(){
        if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
            die;
        }
        $order_id = (int) $this->_get('order_id');
        $order = D('Order')->find($order_id);
        if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
            $this->fengmiMsg('该订单不存在');
        }
        $addr_id = (int) $this->_post('addr_id');
        if (empty($addr_id)) {
            $this->fengmiMsg('请选择一个要配送的地址！');
        }
		//添加优惠劵满减的优惠劵下载的ID
		$download_id = (int) $this->_post('download_id');
		if(!empty($download_id)){
			$coupon_price = D('Coupon')->Obtain_Coupon_Price($order_id,$download_id);
		}else{
			$coupon_price = 0;//防止写入数据库失败
		}
		//添加留言
		$message = $this->_param('message', 'htmlspecialchars');
        D('Order')->save(array('order_id' => $order_id,'addr_id' => $addr_id,'download_id' => $download_id,'coupon_price' => $coupon_price,'message' => $message));
        if (!($code = $this->_post('code'))) {
            $this->fengmiMsg('请选择支付方式！');
        }

		if (false == D('Goods')->check_goods_id_mum($order_id)) {//批量检测库存
			$this->fengmiMsg(D('Goods')->getError(), U('mall/index'));	 
		}
		
        $ua = D('UserAddr');
        $uaddr = $ua->where(array('addr_id' => $order['addr_id']))->find();
        if ($code == 'wait') {
            //如果是货到付款
            D('Order')->save(array('order_id' => $order_id, 'is_daofu' => 1));
            D('Ordergoods')->save(array('is_daofu' => 1), array('where' => array('order_id' => $order_id)));
            D('Payment')->mallSold($order_id);
            D('Payment')->mallPeisong(array($order_id), 1);
			D('Sms')->mallTZshop($order_id);
            D('Weixintmpl')->weixin_notice_goods_user($order_id,$this->uid,1);//商城微信通知货到付款
            $this->fengmiMsg('恭喜您下单成功！', U('mcenter/goods/index',array('aready'=>1)));
        } else {
            $payment = D('Payment')->checkPayment($code);
            if (empty($payment)) {
                $this->fengmiMsg('该支付方式不存在');
            }
            $logs = D('Paymentlogs')->getLogsByOrderId('goods', $order_id);
            //写入支付记录
            $need_pay = D('Order')->useIntegral($this->uid, array($order_id));//再次更新价格
            //更新支付结果
            if (empty($logs)) {
                $logs = array(
					'type' => 'goods', 
					'user_id' => $this->uid, 
					'order_id' => $order_id, 
					'code' => $code, 
					'need_pay' => $need_pay, 
					'create_time' => NOW_TIME, 
					'create_ip' => get_client_ip(), 
					'is_paid' => 0
				);
                //单个付款走的这里，为什么没写入数据库$need_pay
                $logs['log_id'] = D('Paymentlogs')->add($logs);
            } else {
                $logs['need_pay'] = $need_pay;
                $logs['code'] = $code;
                D('Paymentlogs')->save($logs);
            }
            D('Order')->where("order_id={$order_id}")->save(array('need_pay' => $need_pay));
            //再更新一次最终的价格，蜂蜜独创
			D('Weixintmpl')->weixin_notice_goods_user($order_id,$this->uid,1);//商城微信通知货到付款
            $this->fengmiMsg('订单设置完成，即将进入付款。', U('payment/payment', array('log_id' => $logs['log_id'])));
        }
    }
    //团购点评
    public function dianping(){
        $goods_id = (int) $this->_get('goods_id');
        if (!($detail = D('Goods')->find($goods_id))) {
            $this->error('没有该商品');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商品已经被删除');
            die;
        }

        $this->assign('next', LinkTo('mall/dianpingloading', $linkArr, array('goods_id' => $goods_id, 't' => NOW_TIME, 'p' => '0000')));
        $this->assign('detail', $detail);
        $this->display();
    }
    public function dianpingloading(){
        $goods_id = (int) $this->_get('goods_id');
        if (!($detail = D('Goods')->find($goods_id))) {
            die('0');
        }
        if ($detail['closed']) {
            die('0');
        }
        $Goodsdianping = D('Goodsdianping');
        import('ORG.Util.Page');
        $map = array('closed' => 0, 'goods_id' => $goods_id, 'show_date' => array('ELT', TODAY));
        $count = $Goodsdianping->where($map)->count();
        $Page = new Page($count, 5);
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Goodsdianping->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $orders_ids = array();
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $orders_ids[$val['order_id']] = $val['order_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($orders_ids)) {
            $this->assign('pics', D('Goodsdianpingpics')->where(array('order_id' => array('IN', $orders_ids)))->select());
        }
        $this->assign('totalnum', $count);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('detail', $detail);
        $this->display();
    }
	
	//付款前检测库存
    public function goods_mum($order_id){
        $order_id = (int) $order_id;
        $ordergoods_ids = D('Ordergoods')->where(array('order_id' => $order_id))->select();
        foreach ($ordergoods_ids as $k => $v) {
            $goods_num = D('Goods')->where(array('goods_id' => $v['goods_id']))->find();
            if ($goods_num['num'] < $v['num']) {
				$this->fengmiMsg('商品ID' . $v['goods_id'] . '库存不足无法付款',U('mcenter/goods/index',array('aready'=>1)));;
            }
        }
        return false;
    }
}