<?php

class IntegralAction extends CommonAction {

    private $create_fields = array('title','shop_id', 'face_pic', 'integral', 'price', 'num', 'limit_num', 'exchange_num', 'details', 'orderby', 'create_time', 'create_ip');
    private $edit_fields = array('title','shop_id', 'face_pic', 'integral', 'price', 'num', 'limit_num', 'exchange_num', 'details', 'orderby', 'create_time', 'create_ip');

    public function index() {
        $Integralgoods = D('Integralgoods');
        import('ORG.Util.Page'); 
        $map = array('closed'=>0,'shop_id'=>$this->shop_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($shop_id = (int) $this->_param('shop_id')) {
            $map['shop_id'] = $shop_id;
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name', $shop['shop_name']);
            $this->assign('shop_id', $shop_id);
        }
        if ($audit = (int) $this->_param('audit')) {
            $map['audit'] = ($audit === 1 ? 1 : 0);
            $this->assign('audit', $audit);
        }
        $count = $Integralgoods->where($map)->count(); 
        $Page = new Page($count, 25); 
        $show = $Page->show(); 
        $list = $Integralgoods->where($map)->order(array('goods_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
         foreach ($list as $k => $val) {
            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $list[$k] = $val;
        }
        $this->assign('list', $list); 
        $this->assign('page', $show); 
        $this->display();
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Integralgoods');
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('integral/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('产品名称不能为空');
        }
		$data['shop_id'] = $this->shop_id; 
		$data['city_id'] = $this->shop['city_id']; 
		$data['face_pic'] = htmlspecialchars($data['face_pic']);
        if (empty($data['face_pic'])) {
            $this->baoError('请上传产品图片');
        }
        if (!isImage($data['face_pic'])) {
            $this->baoError('产品图片格式不正确');
        } 
        $data['integral'] = (int) $data['integral'];
        if (empty($data['integral'])) {
            $this->baoError('兑换积分不能为空');
        } $data['price'] = (int) $data['price'];
        if (empty($data['price'])) {
            $this->baoError('市场价格不能为空');
        } $data['num'] = (int) $data['num'];
        if (empty($data['num'])) {
            $this->baoError('库存数量不能为空');
        } $data['limit_num'] = (int) $data['limit_num'];
        if (empty($data['limit_num'])) {
            $this->baoError('限制单用户兑换数量不能为空');
        } $data['exchange_num'] = (int) $data['exchange_num'];
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('商品介绍不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('商品介绍含有敏感词：' . $words);
        } 
		$data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        $data['orderby'] = (int) $data['orderby'];
        return $data;
    }

    public function edit($goods_id = 0) {
        if ($goods_id = (int) $goods_id) {
            $obj = D('Integralgoods');
            if (!$detail = $obj->find($goods_id)) {
                $this->baoError('请选择要编辑的积分商品');
            }
			if ($detail['shop_id'] != $this->shop_id) {
                $this->baoError('请不要非法操作');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['goods_id'] = $goods_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('integral/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的积分商品');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('产品名称不能为空');
        } 
		$data['shop_id'] = $this->shop_id; 
		$data['city_id'] = $this->shop['city_id'];
		$data['face_pic'] = htmlspecialchars($data['face_pic']);
        if (empty($data['face_pic'])) {
            $this->baoError('请上传产品图片');
        }
        if (!isImage($data['face_pic'])) {
            $this->baoError('产品图片格式不正确');
        }
        $data['integral'] = (int) $data['integral'];
        if (empty($data['integral'])) {
            $this->baoError('兑换积分不能为空');
        } $data['price'] = (int) $data['price'];
        if (empty($data['price'])) {
            $this->baoError('市场价格不能为空');
        } $data['num'] = (int) $data['num'];
        if (empty($data['num'])) {
            $this->baoError('库存数量不能为空');
        } $data['limit_num'] = (int) $data['limit_num'];
        if (empty($data['limit_num'])) {
            $this->baoError('限制单用户兑换数量不能为空');
        } $data['exchange_num'] = (int) $data['exchange_num'];
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('商品介绍不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('商品介绍含有敏感词：' . $words);
        } 
		$data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        $data['orderby'] = (int) $data['orderby'];
        return $data;
    }

    public function delete($goods_id = 0) {
            $goods_id = (int) $goods_id;
			if(!empty($goods_id)){
				$obj = D('Integralgoods');
				if (!$detail = $obj->find($goods_id)) {
					$this->baoError('请选择要编辑的积分商品');
				}
				if ($detail['shop_id'] != $this->shop_id) {
					$this->baoError('请不要非法操作');
				}
				$obj->save(array('goods_id' => $goods_id, 'closed' => 1));
				$this->baoSuccess('删除成功！', U('integralgoods/index'));
			}else{
				$this->baoError('请选择要删除的积分商品');
			}
    }

	//积分兑换订单列表
	public  function order(){
       $Integralexchange = D('Integralexchange');
       import('ORG.Util.Page');
       $map = array('shop_id'=>$this->shop_id);
       if (($bg_date = $this->_param('bg_date', 'htmlspecialchars') ) && ($end_date = $this->_param('end_date', 'htmlspecialchars'))) {
            $bg_time = strtotime($bg_date);
            $end_time = strtotime($end_date);
            $map['create_time'] = array(array('ELT', $end_time), array('EGT', $bg_time));
            $this->assign('bg_date', $bg_date);
            $this->assign('end_date', $end_date);
        } else {
            if ($bg_date = $this->_param('bg_date', 'htmlspecialchars')) {
                $bg_time = strtotime($bg_date);
                $this->assign('bg_date', $bg_date);
                $map['create_time'] = array('EGT', $bg_time);
            }
            if ($end_date = $this->_param('end_date', 'htmlspecialchars')) {
                $end_time = strtotime($end_date);
                $this->assign('end_date', $end_date);
                $map['create_time'] = array('ELT', $end_time);
            }
        }
        if ($user_id = (int) $this->_param('user_id')) {
            $users = D('Users')->find($user_id);
            $this->assign('nickname', $users['nickname']);
            $this->assign('user_id', $user_id);
            $map['user_id'] = $user_id;
        }
        if ($shop_id = (int) $this->_param('shop_id')) {
            $map['shop_id'] = $shop_id;
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name', $shop['shop_name']);
            $this->assign('shop_id', $shop_id);
        }
        if (isset($_GET['st']) || isset($_POST['st'])) {
            $st = (int) $this->_param('st');
            if ($st != 999) {
                $map['status'] = $st;
            }
            $this->assign('st', $st);
        } else {
            $this->assign('st', 999);
        }
       $count      = $Integralexchange->where($map)->count();
       $Page       = new Page($count,15);
       $show       = $Page->show();
       $list = $Integralexchange->where($map)->order(array('exchange_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
       $user_ids = $shop_ids= $good_ids = $addr_ids = array();
       foreach($list  as  $val){
           $user_ids[$val['user_id']] = $val['user_id'];
           $shop_ids[$val['shop_id']] = $val['shop_id'];
           $good_ids[$val['goods_id']] = $val['goods_id'];
           $addr_ids[$val['addr_id']]  = $val['addr_id'];
       }
       $this->assign('areas',D('Area')->fetchAll());
       $this->assign('business',D('Business')->fetchAll());
       $this->assign('users',D('Users')->itemsByIds($user_ids));
       $this->assign('shops',D('Shop')->itemsByIds($shop_ids));
       $this->assign('goods',D('Integralgoods')->itemsByIds($good_ids));
       $this->assign('addrs',D('Useraddr')->itemsByIds($addr_ids));
       $this->assign('list',$list);
       $this->assign('page',$show);
       $this->display(); 
    }

	//积分兑换审核列表
    public function audit($exchange_id = 0){
         $exchange_id = (int)$exchange_id;
		 if(!empty($exchange_id)){
			$obj =D('Integralexchange');
			if (!$detail = $obj->find($exchange_id)) {
				$this->baoError('请选择要审核的订单');
			}
			if ($detail['shop_id'] != $this->shop_id) {
				$this->baoError('请不要非法操作');
			}
            $obj->save(array('exchange_id'=>$exchange_id,'audit'=>1));
            $this->baoSuccess('审核成功！',U('integral/order')); 
		 }else{
			$this->baoError('请选择要审核的积分兑换');
		 }
         
    }

}
