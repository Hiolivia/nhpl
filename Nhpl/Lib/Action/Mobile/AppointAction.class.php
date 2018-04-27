<?php
class AppointAction extends CommonAction {
	protected $Activitycates = array();
    public function _initialize() {
        parent::_initialize();
        $this->appointcates = D('Appointcate')->fetchAll();//分类表
        $this->assign('appointcates', $this->appointcates);
		$this->assign('areas', $areas = D('Area')->fetchAll());
		$this->assign('bizs', $biz = D('Business')->fetchAll());
		$this->assign('host',__HOST__);
    }
	
	public function index(){
        $cat = (int) $this->_param('cat');
        $this->assign('cat', $cat);

        $order = $this->_param('order', 'htmlspecialchars');
        $this->assign('order', $order);
		
		$area_id = (int) $this->_param('area_id');
        $this->assign('area_id', $area_id);

        $this->assign('nextpage', linkto('appoint/loaddata', array('cat'=>$cat,'area_id'=>$area_id,'order'=>$order,'t' => NOW_TIME, 'p' => '0000')));
        $this->display();
    }
	
	
    public function loaddata() {
        $Appoint = D('Appoint');
        import('ORG.Util.Page'); 
        $map = array('closed' => 0,'audit' => 1,'end_date' => array('EGT', TODAY));
		
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        //搜索二开结束
		$cates = D('Appointcate')->fetchAll();
        $cat = (int) $this->_param('cat');
        $cate_id = (int) $this->_param('cate_id');
        if ($cat) {
            if (!empty($cate_id)) {
                $map['cate_id'] = $cate_id;
            } else {
                $catids = D('Appointcate')->getChildren($cat);
                if (!empty($catids)) {
                    $map['cate_id'] = array('IN', $catids);
                }
            }
        }
        $this->assign('cat', $cat);
        $this->assign('cate_id', $cate_id);
		
        $area_id = (int) $this->_param('area_id');
        if ($area_id) {
            $map['area_id'] = $area_id;
        }
		
		$order = $this->_param('order', 'htmlspecialchars');
        $orderby = '';
         switch ($order) {
			case 4:
                $orderby = array('views' => 'asc');
                break;
            case 2:
                $orderby = array('yuyue_num' => 'asc');
                break;
            default:
                $orderby = " (ABS(lng - '{$lng}') +  ABS(lat - '{$lat}') ) asc ";
                break;
        }
		$this->assign('order', $order);
		//搜索二开结束
        $count = $Appoint->where($map)->count(); 
        $Page = new Page($count, 10); 
        $show = $Page->show(); 
		
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		
        $list = $Appoint->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
       
        $shop_ids = $cate_ids = array();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
				$cate_ids[$val['cate_id']] = $val['cate_id'];
            }
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
		if ($cate_ids) {
            $this->assign('appoint_cates', D('Appointcate')->itemsByIds($cate_ids));
        }
		
        $this->assign('list', $list); 
        $this->assign('page', $show);
        $this->display(); 
    }



    public function detail($appoint_id) {
        $appoint_id = (int) $appoint_id;
		$Appoint = D('Appoint');
        $this->assign('cates', D('Appointcate')->fetchAll());
		if (!$detail = $Appoint->find($appoint_id)) {
            $this->error('该家政项目不存在！');
            die;
        }
        
		
        $h = date('H',NOW_TIME) + 1;
        $this->assign('h',$h);

		//预约判断
		$sign = D('Appointorder')->where(array('user_id' => $this->uid, 'appoint_id' => $appoint_id))->select();
        if (!empty($sign)) {
            $detail['sign'] = 1;
        } else {
            $detail['sign'] = 0;
        }
		
		$Appoint->updateCount($appoint_id, 'views');//更新浏览量
		$detail['thumb'] = unserialize($detail['thumb']);
		//修复点评开启
		$Appointdianping = D('Appointdianping');
		$pingnum = $Appointdianping->where(array('appoint_id' => $appoint_id))->count();
        $this->assign('pingnum', $pingnum);
        $score = (int) $Appointdianping->where(array('appoint_id' => $appoint_id))->avg('score');
        if ($score == 0) {
            $score = 5;
        }
        $this->assign('score', $score); 

		$this->assign('shops', D('Shop')->find($detail['shop_id']));
        $this->assign('list', $list); 
        $this->assign('page', $show);
		$this->assign('detail', $detail);
        $this->display();

    }
	
	public function dianping(){
        $appoint_id = (int) $this->_get('appoint_id');
        if (!($detail = D('Appoint')->find($appoint_id))) {
            $this->error('没有该家政');
            die;
        }
        if ($detail['closed']) {
            $this->error('该家政已经被删除');
            die;
        }
        $this->assign('next', LinkTo('appoint/dianpingloading', array('appoint_id' => $appoint_id, 't' => NOW_TIME, 'p' => '0000')));
        $this->assign('detail', $detail);
        $this->display();
    }
    public function dianpingloading(){
        $appoint_id = (int) $this->_get('appoint_id');
        if (!($detail = D('Appoint')->find($appoint_id))) {
            die('0');
        }
        if ($detail['closed']) {
            die('0');
        }
        $Appointdianping = D('Appointdianping');
        import('ORG.Util.Page');
        $map = array('closed' => 0, 'appoint_id' => $appoint_id);
        $count = $Appointdianping->where($map)->count();
        $Page = new Page($count, 5);
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Appointdianping->where($map)->order(array('create_time' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $id_ids = array();
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $appoint_ids[$val['appoint_id']] = $val['appoint_id'];
			$order_ids[$val['order_id']] = $val['order_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($appoint_ids)) {
            $this->assign('pics', D('Appointdianpingpics')->where(array('order_id' => array('IN', $order_ids)))->select());
        }
        $this->assign('totalnum', $count);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('detail', $detail);
        $this->display();
    }
	
	public function yuyue($appoint_id) {
		$appoint_id = (int) $appoint_id;
		if (empty($this->uid)) {
            $this->error('登录状态失效!', U('passport/login'));
        }
        if (!($detail = D('Appoint')->find($appoint_id))) {
            $this->error('该家政项目不存在！');
            die;
        }
		$cfg = D('Shopdingsetting')->getCfg(); 
        $this->assign('cfg',$cfg);
		$this->assign('cates', $this->appointcates); 
        $this->assign('detail', $detail);
        $this->display();
	 }
    public function create($appoint_id) {
		if (empty($this->uid)) {
            $this->fengmiMsg('请登录后预约', U('passport/login'));
        }
        if (!$appoint_id = (int) $appoint_id) {
            $this->fengmiMsg('服务类型不能为空');
        }
		
		$cate_id = D('Appoint')->find($appoint_id);
        if (!isset($this->appointcates[$cate_id['cate_id']])) {
            $this->fengmiMsg('暂时没有该服务类型');
        }
		
		//先判断余额
		if ($this->member['money'] < $cate_id['price']){
			$this->fengmiMsg('抱歉，您的余额不足',U('mcenter/money/index'));
		}
		
		$appoint_shop = D('Shop')->find($ids['shop_id']);//商家信息
		$appoint_shop_user = D('Users')->find($appoint_shop['user_id']);//商家信息
		$data['city_id'] = $this->city_id;
		$data['appoint_id'] = $appoint_id;
		$data['user_id'] = (int) $this->uid;
        $data['cate_id'] = $cate_id['cate_id'];
		$data['shop_id'] = $appoint_shop['shop_id'];
        $data['date'] = htmlspecialchars($_POST['date']);
        $data['time'] = htmlspecialchars($_POST['time']);
		
        if(empty($data['date'])|| empty($data['time'])){
            $this->fengmiMsg('服务时间不能为空');
        }
        $data['svctime'] = $data['date'].  " " . $data['time']; 
		
		//判断时间是否过期
		$svctime = $data['date'].' '.$data['time'];
		$appoint_time = strtotime($svctime);
		if (empty($data['time'])) { 
            $this->fengmiMsg('请选择时间');
        }else if($appoint_time < time()){
			$this->fengmiMsg('预约时间已经过期，请选择正确的时间');
		}
		//判断时间过期结束
		
        if (!$data['addr'] = $this->_post('addr', 'htmlspecialchars')) {
            $this->fengmiMsg('服务地址不能为空');
        }
        if (!$data['name'] = $this->_post('name', 'htmlspecialchars')) {
            $this->fengmiMsg('联系人不能为空');
        }
        if (!$data['tel'] = $this->_post('tel', 'htmlspecialchars')) {
            $this->fengmiMsg('联系电话不能为空');
        }
        if (!isMobile($data['tel']) && !isPhone($data['tel'])) {
            $this->fengmiMsg('电话号码不正确');
        }
		$data['need_pay'] = $cate_id['price'];
		$data['status'] = 1;//购买，后期增加退
		
        $data['contents'] = $this->_post('contents', 'htmlspecialchars');
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
		
			
        if ($order_id = D('Appointorder')->add($data)) {
			D('Appoint')->updateCount($appoint_id, 'yuyue_num');
			D('Appoint')->appoint_buy($this->uid,$appoint_id, $cate_id['price'],$order_id);//购买函数
			//短信通知用户预约成功
			$sms_time = $data['date'].'时间'.$data['time'];
			if(!empty($data['tel'])){
				$user_mobile = $data['tel'];
			}else{
				$user_mobile = $this->member['mobile'];	
			}
						
			if($this->_CONFIG['sms']['dxapi'] == 'dy'){
                D('Sms')->DySms($this->_CONFIG['site']['sitename'], 'sms_appoint_TZ_user', $user_mobile, array(
			 	    'sitename'=>$this->_CONFIG['site']['sitename'], 
                    'name' => $data['name'], 
					'time' => $sms_time, 
					'addr' => $data['addr'], 
					'cate_name' => $this->appointcates[$cate_id['cate_id']]['cate_name']
                ));
            }else{
                D('Sms')->sendSms('sms_appoint_TZ_user', $user_mobile, array(
                    'name' => $data['name'], 
					'time' => $sms_time, 
					'addr' => $data['addr'], 
					'cate_name' => $this->appointcates[$cate_id['cate_id']]['cate_name']
                ));
            }

			//邮件通知商家
			if(!empty($appoint_shop_user['email'])){		
				D('Email')->sendMail('email_tz_appoint_yuyue', $appoint_shop_user['email'], '商家家政预约', array(
					'name'=>$data['name'],
					'date'=>$data['date'],
					'time'=>$data['time'],
					'addr'=>$data['addr'],
					'tel'=>$data['tel'],
					'contents'=>$data['contents']
				));
			}
            $this->fengmiMsg('恭喜您预约家政服务成功！', U('appoint/index'));
        }
        $this->fengmiMsg('服务器繁忙');
    }
}

