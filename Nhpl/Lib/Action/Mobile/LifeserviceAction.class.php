<?php
class LifeserviceAction extends CommonAction
{
    protected $Lifeservicecates = array();
    public function _initialize()
    {
        parent::_initialize();
        $life = (int) $this->_CONFIG['operation']['life'];
        if ($life == 0) {
            $this->error('此功能已关闭');
            die;
        }
        $Houseworksetting = D('Houseworksetting');
        $housekeepingcates = D('Housekeepingcate')->fetchAll();
		$this->lifeservicecates =$housekeepingcates;
        foreach ($housekeepingcates as $key => $v) {
            if ($v['cate_id']) {
                $catids = D('Goodscate')->getChildren($v['cate_id']);
                if (!empty($catids)) {
                    $map['cate_id'] = array('IN', $catids);
                } else {
                    $map['cate_id'] = $cat;
                }
            }
            $count = $Houseworksetting->where($map)->count();// 统计当前分类记录
            $housekeepingcates[$key]['count'] = $count;
        }
        $this->assign('housekeepingcates', $housekeepingcates);
    }
    public function index(){
        $cat = (int) $this->_param('cat');
        $this->assign('cat', $cat);
        $linkArr['cat'] = $cat;
        $id = (int) $this->_param('id');
        $order = $this->_param('order', 'htmlspecialchars');
        $this->assign('order', $order);
        $linkArr['order'] = $order;
        $this->assign('nextpage', LinkTo('lifeservice/loaddata', $linkArr, array('t' => NOW_TIME, 'p' => '0000')));
        $this->assign('linkArr', $linkArr);
        $this->display();
    }
    public function loaddata(){
        $houseworksetting = D('Houseworksetting');
        import('ORG.Util.Page');
        $map = array('closed' => 0, 'city_id' => $this->city_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $map['cate_id'] = $cat;
            $this->seodatas['cate_name'] = $this->Activitycates[$cat]['cate_name'];
        }
        $order = $this->_param('order', 'htmlspecialchars');
        switch ($order) {
            case 2:
                $orderby = array('views' => 'desc');
                break;
            default:
                $orderby = array('yuyue_num' => 'desc');
                break;
        }
        $count = $houseworksetting->where($map)->count();
        $Page = new Page($count, 8);
        $show = $Page->show(); 
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $houseworksetting->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = array();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list);
        $this->assign('cates', D('Housekeepingcate')->fetchAll());
        $this->assign('page', $show); 
        $this->assign('linkArr', $linkArr);
        $this->display();
    }
    public function detail($id) {
        $id = (int) $id;
        $this->assign('cates', D('Housekeepingcate')->fetchAll());
        if (!($detail = D('Houseworksetting')->find($id))) {
            $this->error('该家政项目不存在！');
            die;
        }
        $detail = D('Houseworksetting')->find($id);
        $detail['thumb'] = unserialize($detail['thumb']);
        $pingnum = D('Lifeservicedianping')->where(array('id' => $id))->count();
        $this->assign('pingnum', $pingnum);
        $score = (int) D('Lifeservicedianping')->where(array('id' => $id))->avg('score');
        if ($score == 0) {
            $score = 5;
        }
        $this->assign('score', $score); 
        D('Houseworksetting')->updateCount($id, 'views');
        $ids = D('Houseworksetting')->find($id);
        $shops = $ids['shop_id'];
        $this->assign('shops', D('Shop')->itemsByIds($shops));
        $this->assign('detail', $detail);
        $this->display();
    }
    public function dianping(){
        $id = (int) $this->_get('id');
        if (!($detail = D('Houseworksetting')->find($id))) {
            $this->error('没有该家政');
            die;
        }
        if ($detail['closed']) {
            $this->error('该家政已经被删除');
            die;
        }
        $this->assign('next', LinkTo('lifeservice/dianpingloading', $linkArr, array('id' => $id, 't' => NOW_TIME, 'p' => '0000')));
        $this->assign('detail', $detail);
        $this->display();
    }
    public function dianpingloading(){
        $id = (int) $this->_get('id');
        if (!($detail = D('Houseworksetting')->find($id))) {
            die('0');
        }
        if ($detail['closed']) {
            die('0');
        }
        $Lifeservicedianping = D('Lifeservicedianping');
        import('ORG.Util.Page');
        $map = array('closed' => 0, 'id' => $id);
        $count = $Lifeservicedianping->where($map)->count();
        $Page = new Page($count, 5);
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Lifeservicedianping->where($map)->order(array('id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $id_ids = array();
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $id_ids[$val['id']] = $val['id'];
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        if (!empty($id_ids)) {
            $this->assign('pics', D('Lifeservicedianpingpics')->where(array('id' => array('IN', $id_ids)))->select());
        }
        $this->assign('totalnum', $count);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('detail', $detail);
        $this->display();
    }
    //家政点评结束
    public function yuyue($id){
        $id = (int) $id;
        $this->assign('cates', D('Housekeepingcate')->fetchAll()); 
        if (!($detail = D('Houseworksetting')->find($id))) {
            $this->fengmiMsg('该家政项目不存在！');
            die;
        }
		$cfg = D('Shopdingsetting')->getCfg(); //调用定做的时间设置
        $this->assign('cfg',$cfg);
        $this->assign('detail', $detail);
        $this->display();
    }
    public function create($id) {
        if (empty($this->uid)) {
            $this->fengmiMsg('登录状态失效!', U('passport/login'));
        }
       if (!$id = (int) $id) {
            $this->fengmiMsg('服务类型不能为空');
        }
		$cate_id = D('Houseworksetting')->find($id);
        if (!isset($this->lifeservicecates[$cate_id['cate_id']])) {
            $this->fengmiMsg('暂时没有该服务类型');
        }
		
		$lifeservice_shop = D('Shop')->find($ids['shop_id']);//商家信息
		$lifeservice_user = D('Users')->find($lifeservice_shop['user_id']);//用户信息

		$data['id'] = $id;
		$data['user_id'] = (int) $this->uid;
        $data['cate_id'] = $this->lifeservicecates[$cate_id['cate_id']]['cate_name'];
		$data['shop_id'] = $lifeservice_shop['shop_id'];
        $data['date'] = htmlspecialchars($_POST['date']);
        $data['time'] = htmlspecialchars($_POST['time']);
		
		$data['svctime'] = $data['date'].  " " . $data['time']; 
		
		//判断时间是否过期
		$svctime = $data['date'].' '.$data['time'];
		$lifeservice_time = strtotime($svctime);
		if (empty($data['time'])) { 
            $this->fengmiMsg('请选择时间');
        }else if($lifeservice_time < time()){
			$this->fengmiMsg('预约时间已经过期，请选择正确的时间');
		}
		//判断时间过期结束

        if (!($data['addr'] = $this->_post('addr', 'htmlspecialchars'))) {
            $this->fengmiMsg('服务地址不能为空');
        }
        if (!($data['name'] = $this->_post('name', 'htmlspecialchars'))) {
            $this->fengmiMsg('联系人不能为空');
        }
        if (!($data['tel'] = $this->_post('tel', 'htmlspecialchars'))) {
            $this->fengmiMsg('联系电话不能为空');
        }
        if (!isMobile($data['tel']) && !isPhone($data['tel'])) {
            $this->fengmiMsg('电话号码不正确');
        }
        $data['contents'] = $this->_post('contents', 'htmlspecialchars');
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
		
		
        if (D('Housework')->add($data)) {
            D('Houseworksetting')->updateCount($id, 'yuyue_num');
			//短信通知用户预约成功
			$sms_time = $data['date'].$data['time'];
			if(!empty($data['tel'])){
				$user_mobile = $data['tel'];
			}else{
				$user_mobile = $this->member['mobile'];	
			}
			if($this->_CONFIG['sms']['dxapi'] == 'dy'){
                D('Sms')->DySms($this->_CONFIG['site']['sitename'], 'sms_lifeservice_TZ_user', $user_mobile, array(
			 	    'sitename'=>$this->_CONFIG['site']['sitename'], 
                    'name' => $data['name'], 
					'time' => $sms_time, 
					'addr' => $data['addr'], 
					'lifeservice' => $this->lifeservicecates[$cate_id['cate_id']]['cate_name']
                ));
            }else{
                D('Sms')->sendSms('sms_lifeservice_TZ_user', $user_mobile, array(
                    'name' => $data['name'], 
					'time' => $sms_time, 
					'addr' => $data['addr'], 
					'lifeservice' => $this->lifeservicecates[$cate_id['cate_id']]['cate_name']
                ));
            }
			//邮件通知管理员
			$lifeservice = $this->_CONFIG['site']['config_email'];			
			D('Email')->sendMail('email_lifeservice_yuyue', $lifeservice, $this->_CONFIG['site']['sitename'].'管理员：有客户预约'.$this->lifeservicecates[$cate_id['cate_id']]['cate_name'], array(
				'name'=>$data['name'],
				'date'=>$data['date'],
				'time'=>$data['time'],
				'addr'=>$data['addr'],
				'tel'=>$data['tel'],
				'contents'=>$data['contents']
			));
			//邮件通知商家

			if(!empty($shangjia_email)){		
			D('Email')->sendMail('email_sj_lifeservice_yuyue', $lifeservice_user['email'], '尊敬的商家，有客户预约'.$this->lifeservicecates[$cate_id['cate_id']]['cate_name'], array(
				'name'=>$data['name'],
				'date'=>$data['date'],
				'time'=>$data['time'],
				'addr'=>$data['addr'],
				'tel'=>$data['tel'],
				'contents'=>$data['contents']
				));
			}
            $this->fengmiMsg('恭喜您预约家政服务成功！网站会推荐给您最优秀的阿姨帮忙！', U('lifeservice/index'));
        }
        $this->fengmiMsg('服务器繁忙');
    }
}