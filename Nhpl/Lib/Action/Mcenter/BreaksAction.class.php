<?php

class BreaksAction extends CommonAction {

	public function index() {
        $this->assign('nextpage', LinkTo('breaks/loaddata',array('t' => NOW_TIME, 'p' => '0000')));
        $this->mobile_title = '优惠买单';
		$this->display(); // 输出模板
	}
  
    public function loaddata() {
		$breaks = D('Breaksorder');
		import('ORG.Util.Page');
		$map = array('user_id' => $this->uid);
        
		$count = $breaks->where($map)->count();
		$Page = new Page($count, 20);
		$show = $Page->show();
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		$list = $breaks->where($map)->order(array('order_id'=>'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$shop_ids = array();
		foreach ($list as $k => $val) {
            $list[$k]['yh'] = $val['amount'] - $val['need_pay'];
			$shop_ids[$val['shop_id']] = $val['shop_id'];
		}
		$shops = D('Shop')->itemsByIds($shop_ids);
		$this->assign('shops', $shops);
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

    
}