<?php
class AppointAction extends CommonAction {
	protected $Activitycates = array();
    public function _initialize() {
        parent::_initialize();
        $this->appointcates = D('Appointcate')->fetchAll();//分类表
        $this->assign('appointcates', $this->appointcates);
		$this->assign('host',__HOST__);
    }
    public function index() {
        $Appoint = D('Appoint');
        import('ORG.Util.Page'); 
        $map = array('closed' => 0,'audit' => 1,'city_id'=>$this->city_id, 'end_date' => array('EGT', TODAY));
		
		$linkArr = array();
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
                $this->seodatas['cate_name'] = $cates[$cate_id]['cate_name'];
                $linkArr['cat'] = $cat;
                $linkArr['cate_id'] = $cate_id;
            } else {
                $catids = D('Appointcate')->getChildren($cat);
                if (!empty($catids)) {
                    $map['cate_id'] = array('IN', $catids);
                }
                $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
                $linkArr['cat'] = $cat;
            }
        }
        $this->assign('cat', $cat);
        $this->assign('cate_id', $cate_id);
        $area = (int) $this->_param('area');
        if ($area) {
            $map['area_id'] = $area;
            $this->seodatas['area_name'] = $this->areas[$area]['area_name'];
            $linkArr['area'] = $area;
        }
        $this->assign('area_id', $area);
        $business = (int) $this->_param('business');
        if ($business) {
            $map['business_id'] = $business;
            $this->seodatas['business_name'] = $this->bizs[$business]['business_name'];
            $linkArr['business'] = $business;
        }
        $this->assign('business_id', $business);
		
		$order = $this->_param('order', 'htmlspecialchars');
        $orderby = '';
        switch ($order) {
            case 's':
                $orderby = array('yuyue_num' => 'asc');
                $linkArr['order'] = $order;
                break;
            case 'p':
                $orderby = array('price' => 'asc');
                $linkArr['order'] = $order;
                break;
            case 't':
                $orderby = array('create_time' => 'asc');
                $linkArr['order'] = $order;
                break;
            case 'v':
                $orderby = array('views' => 'asc');
                $linkArr['order'] = $order;
                break;
            default:
                $orderby = array('yuyue_num' => 'desc', 'appoint_id' => 'desc');
                break;
        }
		$this->assign('order', $order);
		//搜索二开结束
        $count = $Appoint->where($map)->count(); 
        $Page = new Page($count, 10); 
        $show = $Page->show(); 
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
		$selArr = $linkArr;
        foreach ($selArr as $k => $val) {
            if ($k == 'order' || $k == 'new' || $k == 'freebook' || $k == 'hot' || $k == 'tui') {
                unset($selArr[$k]);
            }
        }
		
		
		
        $this->assign('list', $list); 
		$this->assign('selArr', $selArr);
		$this->assign('cates', $cates);
        $this->assign('page', $show);
		$this->assign('linkArr', $linkArr);
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
		
		// 点评开始
		$Appointdianping = D('Appointdianping');
        import('ORG.Util.Page'); 
        $map = array('closed' => 0, 'appoint_id' => $appoint_id, 'show_date' => array('ELT', TODAY));
        $count = $Appointdianping->where($map)->count();
        $Page = new Page($count, 5); 
        $show = $Page->show(); 
        $list = $Appointdianping->where($map)->order(array('dianping_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $id_ids = array();
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($dianping_ids)) {
            $this->assign('pics', D('Appointdianpingpics')->where(array('dianping_id' => array('IN', $dianping_ids)))->select());
        }
		
		$appointment  = D('Appointorder')->where(array('appoint_id'=>$appoint_id))->order(array('create_time' => 'desc'))->select();
		$this->assign('appointment',$appointment);
		
		$total_score = $Appointdianping->where($map)->sum('score');
		$this->assign('score', $score = round($total_score/$count,2));//评分

		$shop = D('Shop')->find($detail['shop_id']);
		$this->seodatas['cate_name'] = $this->appointcates[$detail['cate_id']]['cate_name'];
        $this->seodatas['cate_area'] = $this->areas[$detail['area_id']]['area_name'];
        $this->seodatas['cate_business'] = $this->bizs[$detail['business_id']]['business_name'];
        $this->seodatas['shop_name'] = $shop['shop_name'];
        $this->seodatas['title'] = $detail['title'];
        $this->seodatas['intro'] = $detail['intro'];
		
		$cfg = D('Shopdingsetting')->getCfg();
        $this->assign('cfg',$cfg);
		
		$Appointcate = D('Appointcate')->fetchAll();
        if ($Appointcate[$detail['cate_id']]['parent_id'] == 0) {
            $this->assign('catstr', $Appointcate[$detail['cate_id']]['cate_name']);
        } else {
            $this->assign('catstr', $Appointcate[$Appointcate[$detail['cate_id']]['parent_id']]['cate_name']);
            $this->assign('cat', $Appointcate[$detail['cate_id']]['parent_id']);
            $this->assign('catestr', $Appointcate[$detail['cate_id']]['cate_name']);
        }

		$this->assign('shops', D('Shop')->find($detail['shop_id']));
        $this->assign('totalnum', $count);
        $this->assign('list', $list); 
        $this->assign('page', $show);
        $userrank = D('user_rank')-> select();
        $this -> assign('userrank',$userrank);	
		$this->assign('detail', $detail);
		$this->assign('height_num', 760);//下拉横条导航
        $this->display();

    }

    public function create($appoint_id) {
		if (empty($this->uid)) {
            $this->ajaxLogin();
        }
        if (!$appoint_id = (int) $appoint_id) {
            $this->baoError('服务类型不能为空');
        }
		$cate_id = D('Appoint')->find($appoint_id);
        if (!isset($this->appointcates[$cate_id['cate_id']])) {
            $this->baoError('暂时没有该服务类型');
        }

		//先判断余额
		if ($this->member['money'] < $cate_id['price']){
			$this->baoSuccess('抱歉，您的余额不足',U('members/money/money'));
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
            $this->baoError('服务时间不能为空');
        }
        $data['svctime'] = $data['date'].  " " . $data['time']; 
		
		//判断时间是否过期
		$svctime = $data['date'].' '.$data['time'];
		$appoint_time = strtotime($svctime);
		if (empty($data['time'])) { 
            $this->baoError('请选择时间');
        }else if($appoint_time < time()){
			$this->baoError('预约时间已经过期，请选择正确的时间');
		}
		//判断时间过期结束
		
        if (!$data['addr'] = $this->_post('addr', 'htmlspecialchars')) {
            $this->baoError('服务地址不能为空');
        }
        if (!$data['name'] = $this->_post('name', 'htmlspecialchars')) {
            $this->baoError('联系人不能为空');
        }
        if (!$data['tel'] = $this->_post('tel', 'htmlspecialchars')) {
            $this->baoError('联系电话不能为空');
        }
        if (!isMobile($data['tel']) && !isPhone($data['tel'])) {
            $this->baoError('电话号码不正确');
        }
		$data['need_pay'] = $cate_id['price'];
		$data['status'] = 1;//购买，后期增加退款
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
            $this->baoSuccess('恭喜您预约家政服务成功！', U('appoint/index'));
        }
        $this->baoError('服务器繁忙');
    }
}

