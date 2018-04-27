<?php
class EleAction extends CommonAction
{
    protected $cart = array();
    public function _initialize(){
        parent::_initialize();
        $this->cart = $this->getcart();
        $this->assign('cartnum', (int) array_sum($this->cart));
        $cate = D('Ele')->getEleCate();
        $this->assign('elecate', $cate);
    }
    public function getcart()
    {
        $shop_id = (int) $this->_param('shop_id');
        $cart = (array) json_decode($_COOKIE['ele']);
        $carts = array();
        foreach ($cart as $kk => $vv) {
            foreach ($vv as $key => $v) {
                $carts[$kk][$key] = (array) $v;
            }
        }
        $ids = $nums = array();
        foreach ($carts[$shop_id] as $k => $val) {
            $ids[$val['product_id']] = $val['product_id'];
            $nums[$val['product_id']] = $val['num'];
        }
        $eleproducts = D('Eleproduct')->itemsByIds($ids);
        foreach ($eleproducts as $k => $val) {
            $eleproducts[$k]['cart_num'] = $nums[$val['product_id']];
            $eleproducts[$k]['total_price'] = $nums[$val['product_id']] * $val['price'];
        }
        return $eleproducts;
    }
    public function main()
    {
        $map = array('is_open' => 1, 'audit' => 1, 'city_id' => $this->city_id);
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        $list = D('Ele')->where($map)->order(" (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ")->limit(0, 10)->select();
        $shop_ids = array();
        foreach ($list as $k => $val) {
            if (!empty($val['shop_id'])) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
        }
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('list', $list);
        $this->display();
    }
    public function index()
    {
        $linkArr = array();
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        $linkArr['keyword'] = $keyword;
        $cate = $this->_param('cate', 'htmlspecialchars');
        $this->assign('cate', $cate);
        $linkArr['cate'] = $cate;
        $order = $this->_param('order', 'htmlspecialchars');
        $this->assign('order', $order);
        $linkArr['order'] = $order;
        $area = (int) $this->_param('area');
        $this->assign('area', $area);
        $linkArr['area'] = $area;
        $business = (int) $this->_param('business');
        $this->assign('business', $business);
        $linkArr['business'] = $business;
        $this->assign('nextpage', LinkTo('ele/loaddata', $linkArr, array('t' => NOW_TIME, 'p' => '0000')));
        $this->assign('linkArr', $linkArr);
        $this->display();
    }
    public function loaddata()
    {
        $ele = D('Ele');
        import('ORG.Util.Page');
        // 导入分页类
        //初始数据
        $map = array('audit' => 1, 'city_id' => $this->city_id);
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
        }
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['shop_name'] = array('LIKE', '%' . $keyword . '%');
        }
        $business = (int) $this->_param('business');
        if ($business) {
            $map['business_id'] = $business;
        }
        $order = $this->_param('order', 'htmlspecialchars');
        $lat = addslashes(cookie('lat'));
        $lng = addslashes(cookie('lng'));
        if (empty($lat) || empty($lng)) {
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        switch ($order) {
            case 'a':
                $orderby = array("(ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') )" => 'asc', 'orderby' => 'asc', 'month_num' => 'desc', 'distribution' => 'asc', 'since_money' => 'asc');
                break;
            case 'p':
                $orderby = array('since_money' => 'asc');
                break;
            case 'v':
                $orderby = array('distribution' => 'asc');
                break;
            case 'd':
                $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
                break;
            case 's':
                $orderby = array('month_num' => 'desc');
                break;
        }
        $cate = $this->_param('cate', 'htmlspecialchars');
        $lists = $ele->order($orderby)->where($map)->select();
        foreach ($lists as $k => $val) {
            if (!empty($cate)) {
                if (strpos($val['cate'], $cate) === false) {
                    unset($lists[$k]);
                }
            }
        }
        $count = count($lists);
        $Page = new Page($count, 10);
        $show = $Page->show();
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = array_slice($lists, $Page->firstRow, $Page->listRows);
        $shop_ids = array();
        foreach ($list as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
            $list[$k]['d'] = getDistance($lat, $lng, $val['lat'], $val['lng']);
            if ($this->closeshopele($val['busihour'])) {
                $list[$k]['bsti'] = 1;
            } else {
                $list[$k]['bsti'] = 0;
            }
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
    public function closeshopele($busihour){
        $timestamp = time();
        $now = date('G.i', $timestamp);
        $close = true;
        if (empty($busihour)) {
            return false;
        }
        foreach (explode(',', str_replace(':', '.', $busihour)) as $period) {
            list($periodbegin, $periodend) = explode('-', $period);
            if ($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend) || $periodbegin < $periodend && $now >= $periodbegin && $now < $periodend) {
                $close = false;
            }
        }
        return $close;
    }
    public function shop(){
        $shop_id = (int) $this->_param('shop_id');
        if (!($detail = D('Ele')->find($shop_id))) {
            $this->error('该餐厅不存在');
        }
        if (!($shop = D('Shop')->find($shop_id))) {
            $this->error('该餐厅不存在');
        }
        $Eleproduct = D('Eleproduct');
        $map = array('closed' => 0, 'audit' => 1, 'shop_id' => $shop_id);
        $list = $Eleproduct->where($map)->order(array('sold_num' => 'desc', 'price' => 'asc'))->select();
        foreach ($list as $k => $val) {
            $list[$k]['cart_num'] = $this->cart[$val['product_id']]['cart_num'];
        }
        $this->assign('list', $list);
        // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('detail', $detail);
        $this->assign('cates', D('Elecate')->where(array('shop_id' => $shop_id, 'closed' => 0))->select());
        $this->assign('shop', $shop);
        $this->display();
    }
    //重写购物车
    public function cart(){
        $cart = null;
		$type = (int) $this->_param('type');
        if ($goods = cookie('ele')) {
            $total = array('num' => 0, 'money' => 0);
            $goods = (array) json_decode($goods);
            $ids = array();
            foreach ($goods as $shop_id => $items) {
                foreach ($items as $k2 => $item) {
                    $item = (array) $item;
                    $total['num'] += $item['num'];
                    $total['money'] += $item['price'] * $item['num'];
                    $ids[] = $item['product_id'];
                    $product_item_num[$item['product_id']] = $item['num'];
                }
            }
            $ids = implode(',', $ids);
            $products = D('Eleproduct')->where('closed=0')->select($ids);
            foreach ($products as $k => $val) {
                $products[$k]['cart_num'] = $product_item_num[$val['product_id']];
            }
            $this->assign('detail', D('Ele')->find($shop_id));
            $this->assign('total', $total);
			$this->assign('shop_id', $shop_id);
            $this->assign('type', $type);
            $this->assign('cartgoods', $products);
        }
        $this->display();
    }
    public function order(){
        if (empty($this->uid)) {
            $this->fengmiMsg('请先登陆', U('passport/login'));
        }
        $num = $this->_post('num', false);
        if (empty($num)) {
            $this->fengmiMsg('您还没有订餐呢');
        }
		
        $shop_id = 0;
        $shops = array();
        $products = array();
        $total = array('money' => 0, 'num' => 0);
        $product_name = array();
        foreach ($num as $key => $val) {
            $key = (int) $key;
            $val = (int) $val;
            if ($val < 1 || $val > 99) {
                $this->fengmiMsg('请选择正确的购买数量');
            }
            $product = D('Eleproduct')->find($key);
            $product_name[] = $product['product_name'];
            if (empty($product)) {
                $this->fengmiMsg('产品不正确');
            }
            $shop_id = $product['shop_id'];
            $product['buy_num'] = $val;
            $products[$key] = $product;
            $shops[$shop_id] = $shop_id;
            $total['money'] += $product['price'] * $val;
            $total['num'] += $val;
        }
        if (count($shops) > 1) {
            $this->fengmiMsg('您购买的商品是2个商户的！');
        }
        if (empty($shop_id)) {
            $this->fengmiMsg('商家不存在');
        }
        $shop = D('Ele')->find($shop_id);
        if (empty($shop)) {
            $this->fengmiMsg('该商家不存在');
        }
        if (!$shop['is_open']) {
            $this->fengmiMsg('商家已经打烊，实在对不住客官');
        }
		$settlement_price = (int) ($total['money'] - ($total['money']* $shop['rate'] / 1000));//先算出结算价
        $total['money'] += $shop['logistics'];
        $total['need_pay'] = $total['money'];
        //后面要用到计算
        if ($shop['since_money'] > $total['money']) {
            $this->fengmiMsg('客官，您再订点吧！');
        }
        if ($shop['is_new'] && !D('Eleorder')->checkIsNew($this->uid, $shop_id)) {
            //如果是新单
            if ($total['money'] >= $shop['full_money']) {
                //满足新单的条件 立马减几块钱
                $num1 = (int) (($total['money'] - $shop['full_money']) / 1000);
                //10块钱加1规则
                $total['new_money'] = $shop['new_money'] + $num1 * 100;
                $total['need_pay'] = $total['need_pay'] - $total['new_money'];//规则有问题
            }
        }
        
        $month = date('Ym', NOW_TIME);
        if ($order_id = D('Eleorder')->add(array(
			'user_id' => $this->uid, 
			'shop_id' => $shop_id, 
			'total_price' => $total['money'], 
			'need_pay' => $total['need_pay'], 
			'num' => $total['num'], 
			'new_money' => (int) $total['new_money'], 
			'logistics' => $shop['logistics'], 
			'settlement_price' => $settlement_price, 
			'status' => 0, 
			'create_time' => NOW_TIME, 
			'create_ip' => get_client_ip(), 
			'is_pay' => 0, 
			'month' => $month
		))) {
            foreach ($products as $val) {
                D('Eleorderproduct')->add(array(
					'order_id' => $order_id, 
					'product_id' => $val['product_id'], 
					'num' => $val['buy_num'], 
					'total_price' => $val['price'] * $val['buy_num'], 
					'month' => $month
				));
            }
            cookie('eleproduct', null);
            cookie('ele', null);
            $this->fengmiMsg('下单成功！您可以选择配送地址!', U('ele/pay', array('order_id' => $order_id)));
        }
        $this->fengmiMsg('创建订单失败！');
    }
	
	  public function message(){
        $order_id = (int) $this->_get('order_id');
        if (!($detail = D('Eleorder')->find($order_id))) {
            $this->fengmiMsg('没有该订单');
            die;
        }
        if ($detail['status'] != 0) {
            $this->fengmiMsg('参数错误');
            die;
        }
		$ele_shop = D('Ele')->find($detail['shop_id']);
		
		$tags = $ele_shop['tags'];
		$tagsarray = array();
		if(!empty($tags)){
			$tagsarray = explode(',',$tags);
		}

		//开始处理
        if ($this->isPost()) {
            if ($message = $this->_param('message', 'htmlspecialchars')) {
                $data = array('order_id' => $order_id, 'message' => $message);
                if (D('Eleorder')->save($data)) {
                    $this->fengmiMsg('添加留言成功', U('mobile/ele/pay',array('order_id'=>$detail['order_id'])));
                }
            }
            $this->fengmiMsg('请填写留言');
        } else {
            $this->assign('detail', $detail);
			$this->assign('tagsarray', $tagsarray);
            $this->display();
        }

    }
	
    public function pay()
    {
        if (empty($this->uid)) {
            header('Location:' . U('passport/login'));
            die;
        }
        $this->check_mobile();
        $order_id = (int) $this->_get('order_id');
        $order = D('Eleorder')->find($order_id);
        if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
            $this->error('该订单不存在');
            die;
        }
        $this->assign('shop', D('Ele')->find($order['shop_id']));
        $ordergoods = D('Eleorderproduct')->where(array('order_id' => $order_id))->select();
        $goods = array();
        foreach ($ordergoods as $key => $val) {
            $goods[$val['product_id']] = $val['product_id'];
        }
        $products = D('Eleproduct')->itemsByIds($goods);
        $this->assign('products', $products);
        $this->assign('ordergoods', $ordergoods);
        $useraddr_is_default = D('Useraddr')->where(array('user_id' => $this->uid, 'is_default' => 1))->limit(0, 1)->select();
        $useraddrs = D('Useraddr')->where(array('user_id' => $this->uid))->limit(0, 1)->select();
        if (!empty($useraddr_is_default)) {
            $this->assign('useraddr', $useraddr_is_default);
        } else {
            $this->assign('useraddr', $useraddrs);
        }
        $this->assign('citys', D('City')->fetchAll());
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->assign('order', $order);
        $eles = D('Ele')->find($order['shop_id']);
        if ($eles['is_pay'] == 1) {
            $payment = D('Payment')->getPayments(true);
        } else {
            $payment = D('Payment')->getPayments_delivery(true);
        }
        $this->assign('payment', $payment);
        $this->display();
    }
    public function pay2()
    {
        if (empty($this->uid)) {
            $this->ajaxLogin();
        }
        $order_id = (int) $this->_get('order_id');
        $order = D('Eleorder')->find($order_id);
        if (empty($order) || $order['status'] != 0 || $order['user_id'] != $this->uid) {
            $this->fengmiMsg('该订单不存在');
            die;
        }
        $addr_id = (int) $this->_post('addr_id');
        $uaddr = D('Useraddr')->where('addr_id =' . $addr_id)->find();
        if (empty($addr_id)) {
            $this->fengmiMsg('请选择一个要配送的地址！');
        }
        $mobile = D('Users')->where(array('user_id' => $this->uid))->getField('mobile');
        if (!$mobile) {
            $this->fengmiMsg('请先绑定手机号码再提交！');
        }
        D('Eleorder')->save(array('addr_id' => $addr_id, 'order_id' => $order_id));
        if (!($code = $this->_post('code'))) {
            $this->fengmiMsg('请选择支付方式！');
        }
        //为写入物流记录，查询商家类型
        $dv = D('DeliveryOrder');
        $shop = D('Shop');
        $fshop = $shop->where('shop_id =' . $order['shop_id'])->find();
        if ($code == 'wait') {
            //如果是货到付款
            //如果是货到付款，该订单已经下单了，并且商家是配送员配送
            if ($fshop['is_pei'] == 0) {
                $dv_data = array(
					'type' => 1, 
					'type_order_id' => $order['order_id'], 
					'delivery_id' => 0, 
					'shop_id' => $order['shop_id'], 
					'city_id' => $fshop['city_id'], 
					'lat' => $fshop['lat'], 
					'lng' => $fshop['lng'], 
					'user_id' => $order['user_id'], 
					'shop_name' => $fshop['shop_name'], 
					'shop_addr' => $fshop['addr'], 
					'shop_mobile' => $fshop['tel'], 
					'user_name' => $uaddr['name'], 
					'user_addr' => $uaddr['addr'], 
					'user_mobile' => $uaddr['mobile'], 
					'create_time' => time(), 
					'update_time' => 0, 
					'status' => 0
				);
                $dv->add($dv_data);
                D('Eleorder')->save(array('order_id' => $order_id, 'status' => 1));
            }
			
            $member = D('Users')->find($order['user_id']);
            $shop_id = $order['shop_id'];
            //外卖打印开始
            $msg .= '@@2点菜清单__________NO:' . $order['order_id'] . '\r';
            $msg .= '店名：' . $fshop['shop_name'] . '\r';
            $msg .= '联系人：' . $member['nickname'] . '\r';
            $msg .= '电话：' . $member['mobile'] . '\r';
            $msg .= '客户地址：' . $uaddr['addr'] . '\r';
            $msg .= '用餐时间：' . date('Y-m-d H:i:s', $order['create_time']) . '左右\r';
            $msg .= '用餐地址：' . $fshop['addr'] . '\r';
            $msg .= '商家电话：' . $fshop['tel'] . '\r';
            $msg .= '----------------------\r';
            $msg .= '@@2菜品明细\r';
            $products = D('Eleorderproduct')->where(array('order_id' => $order['order_id']))->select();
            foreach ($products as $key => $value) {
                $product = D('Eleproduct')->where(array('product_id' => $value['product_id']))->find();
                $msg .= $key + 1 . '.' . $product['product_name'] . '————' . $product['price'] / 100 . '元\r';
            }
            $msg .= '----------------------\r';
            $msg .= '外送费用：' . $order['logistics'] / 100 . '元\r';
            $msg .= '包装费用：' . $order['packfee'] / 100 . '元\r';
            $msg .= '菜品金额：' . $order['total_price'] / 100 . '元\r';
            $msg .= '应付金额：' . $order['need_pay'] / 100 . '元\r';
			$msg .= '留言：'.$order['message'].'\r';
            $result = D('Print')->printOrder($msg, $shop_id);
            Log::record('打印订单结果：' . $result);//外卖货到付款打印结束
            
            D('Sms')->eleTZshop($order_id);
			D('Weixintmpl')->weixin_notice_ele_user($order_id,$this->uid,0);//外卖微信通知货到付款
            $this->fengmiMsg('货到付款您下单成功！', U('mcenter/eleorder/index'));
        } else {
            $payment = D('Payment')->checkPayment($code);
            if (empty($payment)) {
                $this->error('该支付方式不存在');
            }
            $logs = D('Paymentlogs')->getLogsByOrderId('ele', $order_id);
            if (empty($logs)) {
                $logs = array(
					'type' => 'ele', 
					'user_id' => $this->uid, 
					'order_id' => $order_id, 
					'code' => $code, 
					'need_pay' => $order['need_pay'], 
					'create_time' => NOW_TIME, 
					'create_ip' => get_client_ip(), 
					'is_paid' => 0
				);
                $logs['log_id'] = D('Paymentlogs')->add($logs);
            } else {
                $logs['need_pay'] = $order['need_pay'];
                $logs['code'] = $code;
                D('Paymentlogs')->save($logs);
            }
			D('Weixintmpl')->weixin_notice_ele_user($order_id,$this->uid,1);//外卖微信通知货到付款
            D('Sms')->eleTZshop($order_id);

            $this->fengmiMsg('选择支付方式成功！下面请进行支付！', U('payment/payment', array('log_id' => $logs['log_id'])));
        }
    }
    public function ajax(){
        $this->cart = cookie('eleproduct');
        $num = count($this->cart);
        $num = $num + 1;
        die("{$num}");
    }
    private function getShop($shop, $lng, $lat){
        foreach ($shop as $k => $v) {
            $shop[$k]['d'] = getDistanceNone($lat, $lng, $v['lat'], $v['lng']);
            if ($shop[$k]['d'] > 20000) {
                unset($shop[$k]);
            }
        }
        return $shop;
    }
    public function favorites(){
        if (empty($this->uid)) {
            $this->ajaxLogin();
        }
        $shop_id = (int) $this->_get('shop_id');
        if (!($detail = D('Shop')->find($shop_id))) {
            $this->error('没有该商家');
        }
        if ($detail['closed']) {
            $this->error('该商家已经被删除');
        }
        if (D('Shopfavorites')->check($shop_id, $this->uid)) {
            $this->error('您已经收藏过了！');
        }
        $data = array('shop_id' => $shop_id, 'user_id' => $this->uid, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip());
        if (D('Shopfavorites')->add($data)) {
            $this->success('恭喜您收藏成功！', U('ele/detail', array('shop_id' => $shop_id)));
        }
        $this->error('收藏失败！');
    }
    public function detail()
    {
        $shop_id = (int) $this->_param('shop_id');
        if (!($detail = D('Ele')->find($shop_id))) {
            $this->error('没有该商家');
            die;
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            $this->error('该商家不存在');
            die;
        }
        $this->assign('detail', $detail);
        $this->assign('shop', D('Shop')->find($shop_id));
        $this->assign('ex', D('Shopdetails')->find($shop_id));
        $this->display();
    }
    public function dianping(){
        $shop_id = (int) $this->_get('shop_id');
        if (!($detail = D('Ele')->find($shop_id))) {
            $this->error('没有该商家');
            die;
        }
        if ($detail['closed']) {
            $this->error('该商家已经被删除');
            die;
        }
        $this->assign('detail', $detail);
        $this->display();
    }
    public function dianpingloading()
    {
        $shop_id = (int) $this->_get('shop_id');
        if (!($detail = D('Ele')->find($shop_id))) {
            die('0');
        }
        if ($detail['closed'] != 0 || $detail['audit'] != 1) {
            die('0');
        }
        $Eledianping = D('Eledianping');
        import('ORG.Util.Page');
        $map = array('closed' => 0, 'shop_id' => $shop_id, 'show_date' => array('ELT', TODAY));
        $count = $Eledianping->where($map)->count();
        $Page = new Page($count, 5);
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $show = $Page->show();
        $list = $Eledianping->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $order_ids = array();
        foreach ($list as $k => $val) {
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($order_ids)) {
            $this->assign('pics', D('Eledianpingpics')->where(array('order_id' => array('IN', $order_ids)))->select());
        }
        $this->assign('totalnum', $count);
        $this->assign('list', $list);
        $this->assign('detail', $detail);
        $this->display();
    }
}