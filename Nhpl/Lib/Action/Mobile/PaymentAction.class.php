<?php

class PaymentAction extends CommonAction {

	protected function ele_success($message, $detail) {
		$order_id = $detail['order_id'];
		$eleorder = D('Eleorder') -> find($order_id);
		$detail['single_time'] = $eleorder['create_time'];
		$detail['settlement_price'] = $eleorder['settlement_price'];
		$detail['new_money'] = $eleorder['new_money'];
		$detail['fan_money'] = $eleorder['fan_money'];
		$addr_id = $eleorder['addr_id'];
		$product_ids = array();
		$ele_goods = D('Eleorderproduct') -> where(array('order_id' => $order_id)) -> select();
		foreach ($ele_goods as $k => $val) {
			if (!empty($val['product_id'])) {
				$product_ids[$val['product_id']] = $val['product_id'];
			}
		}
		$addr = D('Useraddr') -> find($addr_id);
		$this -> assign('addr', $addr);
		$this -> assign('ele_goods', $ele_goods);
		$this -> assign('products', D('Eleproduct') -> itemsByIds($product_ids));
		$this -> assign('message', $message);
		$this -> assign('detail', $detail);
		$this -> assign('paytype', D('Payment') -> getPayments());
		$this -> display('ele');
	}
	
	  protected function hotel_success($message, $detail) {
        $order_id = (int)$detail['order_id'];
        $order = D('Hotelorder')->find($order_id);
        $detail['single_time'] = $order['create_time'];
        $room = D('Hotelroom')->find($order['room_id']);
        $hotel = D('Hotel')->find($room['hotel_id']);
        $this->assign('hotel',$hotel);
        $this->assign('room',$room);
        $this->assign('message', $message);
        $this->assign('detail', $detail);
        $this->assign('order',$order);
        $this->assign('paytype', D('Payment')->getPayments());
        $this->display('hotel');
    }

	protected function goods_success($message, $detail) {
		$order_ids = array();
		if (!empty($detail['order_id'])) {
			$order_ids[] = $detail['order_id'];
		} else {
			$order_ids = explode(',', $detail['order_ids']);
		}
		$goods = $good_ids = $addrs = array();
		foreach ($order_ids as $k => $val) {
			if (!empty($val)) {
				$order = D('Order') -> find($val);
				$addr = D('Useraddr') -> find($order['addr_id']);
				$ordergoods = D('Ordergoods') -> where(array('order_id' => $val)) -> select();
				foreach ($ordergoods as $a => $v) {
					$good_ids[$v['goods_id']] = $v['goods_id'];
				}
			}
			$goods[$k] = $ordergoods;
			$addrs[$k] = $addr;
		}
		$this -> assign('addr', $addrs[0]);
		$this -> assign('goods', $goods);
		$this -> assign('good', D('Goods') -> itemsByIds($good_ids));
		$this -> assign('detail', $detail);
		$this -> assign('message', $message);
		$this -> assign('paytype', D('Payment') -> getPayments());
		$this -> display('goods');
	}

	public function detail($order_id) {
		$dingorder = D('Shopdingorder');
		$dingyuyue = D('Shopdingyuyue');
		$dingmenu = D('Shopdingmenu');
		if (!$order = $dingorder -> where('order_id = ' . $order_id) -> find()) {
			$this -> baoError('该订单不存在');
		} else if (!$yuyue = $dingyuyue -> where('ding_id = ' . $order['ding_id']) -> find()) {
			$this -> baoError('该订单不存在');
		} else if ($yuyue['user_id'] != $this -> uid) {
			$this -> error('非法操作');
		} else {
			$arr = $dingorder -> get_detail($this -> shop_id, $order, $yuyue);
			$menu = $dingmenu -> shop_menu($this -> shop_id);
			$this -> assign('yuyue', $yuyue);
			$this -> assign('order', $order);
			$this -> assign('order_id', $order_id);
			$this -> assign('arr', $arr);
			$this -> assign('menu', $menu);
			$this -> display();
		}
	}


public function booking($order_id) {
		$Bookingorder = D('Bookingorder');
		$Bookingyuyue = D('Bookingyuyue');
		$Bookingmenu = D('Bookingmenu');
		if (!$order = $Bookingorder -> where('order_id = ' . $order_id) -> find()) {
			$this -> baoError('该订单不存在');
		} else if (!$yuyue = $Bookingyuyue  -> where('ding_id = ' . $order['ding_id']) -> find()) {
			$this -> baoError('该订单不存在');
		} else if ($yuyue['user_id'] != $this -> uid) {
			$this -> error('非法操作');
		} else {
			$arr = $Bookingorder -> get_detail($this -> shop_id, $order, $yuyue);
			$menu = $Bookingmenu -> shop_menu($this -> shop_id);
			$this -> assign('yuyue', $yuyue);
			$this -> assign('order', $order);
			$this -> assign('order_id', $order_id);
			$this -> assign('arr', $arr);
			$this -> assign('menu', $menu);
			$this -> display();
		}
	}

	protected function booking_success($message, $detail) {
		$Bookingorder = D('Bookingorder');
		$Bookingyuyue = D('Bookingyuyue');
		$Bookingmenu = D('Bookingmenu');

		if (!$order = $Bookingorder -> where('order_id = ' . $detail['order_id']) -> find()) {
			$this -> error('该订单不存在');
		} else if (!$yuyue = $Bookingyuyue -> where('ding_id = ' . $order['ding_id']) -> find()) {
			$this -> error('该订单不存在');
		} else if ($yuyue['user_id'] != $this -> shop_id) {
			$this -> error('非法操作');
		} else {
			$arr = $Bookingorder -> get_detail($yuyue['shop_id'], $order, $yuyue);
			$menu = $Bookingmenu -> shop_menu($yuyue['shop_id']);
			$this -> assign('yuyue', $yuyue);
			$this -> assign('order', $order);
			$this -> assign('order_id', $detail['order_id']);
			$this -> assign('arr', $arr);
			$this -> assign('menu', $menu);
			$this -> assign('message', $message);
			$this -> assign('paytype', D('Payment') -> getPayments());
			$this -> display('ding');
		}
	}

	protected function ding_success($message, $detail) {
		$dingorder = D('Shopdingorder');
		$dingyuyue = D('Shopdingyuyue');
		$dingmenu = D('Shopdingmenu');

		if (!$order = $dingorder -> where('order_id = ' . $detail['order_id']) -> find()) {
			$this -> error('该订单不存在');
		} else if (!$yuyue = $dingyuyue -> where('ding_id = ' . $order['ding_id']) -> find()) {
			$this -> error('该订单不存在');
		} else if ($yuyue['user_id'] != $this -> shop_id) {
			$this -> error('非法操作');
		} else {
			$arr = $dingorder -> get_detail($yuyue['shop_id'], $order, $yuyue);
			$menu = $dingmenu -> shop_menu($yuyue['shop_id']);
			$this -> assign('yuyue', $yuyue);
			$this -> assign('order', $order);
			$this -> assign('order_id', $detail['order_id']);
			$this -> assign('arr', $arr);
			$this -> assign('menu', $menu);
			$this -> assign('message', $message);
			$this -> assign('paytype', D('Payment') -> getPayments());
			$this -> display('ding');
		}
	}

	protected function other_success($message, $detail) {
		//dump($detail);
		$tuanorder = D('Tuanorder') -> find($detail['order_id']);
		if (!empty($tuanorder['branch_id'])) {
			$branch = D('Shopbranch') -> find($tuanorder['branch_id']);
			$addr = $branch['addr'];
		} else {
			$shop = D('Shop') -> find($tuanorder['shop_id']);
			$addr = $shop['addr'];
		}

		$this -> assign('addr', $addr);
		$tuans = D('Tuan') -> find($tuanorder['tuan_id']);
		$this -> assign('tuans', $tuans);
		$this -> assign('tuanorder', $tuanorder);
		$this -> assign('message', $message);
		$this -> assign('detail', $detail);
		$this -> assign('paytype', D('Payment') -> getPayments());
		$this -> display('other');
	}

	protected function pintuan_success($message, $detail) {
		$tuan = D('Porder') -> find($detail['order_id']);
		//var_dump($detail);die;
		if ($tuan['tstatus'] == 0) {
			D('Porder') -> where(array('id' => $detail['order_id'])) -> save(array('order_status' => 3));
			header("Location:" . U('mcenter/pintuan/groups'));
		} elseif ($tuan['tstatus'] == 1 || $tuan['tstatus'] == 2) {
			$Ptuanteam = D('Ptuanteam') -> where(array('tuan_id' => $tuan['tuan_id'], 'tuan_status' => 2)) -> select();
			$tuanrenCount = count($Ptuanteam);
			if ($tuan['renshu'] <= $tuanrenCount) {
				D('Ptuan') -> save(array('id' => $tuan['tuan_id'], 'tuan_status' => 3, 'success_time' => time()));
				foreach ($Ptuanteam as $key => $val) {
					$uid = $val['user_id'];
					D('Porder') -> where(array('id' => $val['order_id'], 'order_status' => 2)) -> save(array('tuan_status' => 3, 'order_status' => 3));
					//====================拼团成功通知===========================
					include_once "Baocms/Lib/Net/Wxmesg.class.php";
					$_data_ctsuccess = array(//整体变更
						'url' => "http://" . $_SERVER['HTTP_HOST'] . "/mobile/pintuan/tuan/id/" . $tuan['tuan_id'] . ".html", 
						'topcolor' => '#F55555', 
						'first' => '您参团的商品［' . $tuan['goods_name'] . '］已组团成功，即将发货，请注意查收！', 
						'payprice' => round($detail['need_pay'] / 100, 2) . '元', 
						'orderno' => $detail['log_id'], 
						'remark' => '点击查看订单详情', 
					);
					$ctsuccess_data = Wxmesg::ctsuccess($_data_ctsuccess);
					$return = Wxmesg::net($uid, 'OPENTM401153728', $ctsuccess_data);
					//结束
					//====================拼团成功通知===============================
				}

			}
			header("Location:" . U('pintuan/tuan', array('id' => $tuan['tuan_id'])));
		}
	}

	public function respond() {
		$code = $this -> _get('code');
		if (empty($code)) {
			$this -> error('没有该支付方式！');
			die ;
		}
		$ret = D('Payment') -> respond($code);
		if ($ret == false) {
			$this -> error('支付验证失败！');
			die ;
		}
		if ($this -> isPost()) {
			echo 'SUCESS';
			die ;
		}
		$type = D('Payment') -> getType();
		$log_id = D('Payment') -> getLogId();
		$detail = D('Paymentlogs') -> find($log_id);
		if (!empty($detail)) {
			if ($detail['type'] == 'ele') {

				$this -> ele_success('恭喜您支付成功啦！', $detail);

			} elseif ($detail['type'] == 'ding') {
				$this -> ding_success('恭喜您支付成功啦！', $detail);
			}elseif ($detail['type'] == 'booking') {//订座
				$this -> booking_success('恭喜您支付成功啦！', $detail);
			} elseif ($detail['type'] == 'goods') {
				if (empty($detail['order_id'])) {
					$this -> success('合并付款成功', U('mcenter/goods/index'));
				} else {
					$this -> goods_success('恭喜您支付成功啦！', $detail);
				}
			}elseif ($type == 'hotel') {//酒店支付
                $this->hotel_success('恭喜您支付成功啦！', $detail);
            }  elseif ($detail['type'] == 'gold' || $detail['type'] == 'money') {
				$this -> success('恭喜您充值成功', U('mcenter/index/index'));
			} elseif ($detail['type'] == 'pintuan') {
				$this -> pintuan_success('恭喜您支付成功啦！', $detail);
			} else {
				$this -> other_success('恭喜您支付成功啦！', $detail);
			}
		} else {
			$this -> success('支付成功！', U('mcenter/index/index'));
		}

	}

	public function respondapp() {
		$logs_id = I('Logs_id', '', 'intval,trim');
		$ok = I('Ok', '', 'intval,trim');
		if ($ok == 1) {
			D('Payment') -> logsPaid($logs_id);
			$order = D('Paymentlogs') -> where(array('log_id' => $logs_id)) -> find();
			switch ($order['type']) {
				case 'goods' :
					$outArr = array('tstatus' => 200, );
							echo json_encode($outArr);
							exit ;
					break;
					case 'money' :
					$outArr = array('tstatus' => 200, );
							echo json_encode($outArr);
							exit ;
					break;
				case 'pintuan' :
					$Porder = D('Porder') -> where(array('id' => $order['order_id'])) -> find();
					$tstatus = $Porder['tstatus'];
					$tuan_id = $Porder['tuan_id'];
					if (!empty($Porder)) {
						if ($tstatus == 0) {
							D('Porder') -> where(array('id' => $order['order_id'])) -> save(array('order_status' => 3));
							$outArr = array('tstatus' => 200, );
							echo json_encode($outArr);
							exit ;
						} else if ($tstatus == 1 || $tstatus == 2) {
							$Ptuanteam = D('Ptuanteam') -> where(array('tuan_id' => $tuan_id, 'tuan_status' => 2)) -> select();
							$tuanrenCount = count($Ptuanteam);
							if ($Porder['renshu'] <= $tuanrenCount) {
								D('Ptuan') -> save(array('id' => $tuan_id, 'tuan_status' => 3, 'success_time' => time()));
								foreach ($Ptuanteam as $key => $val) {
									$uid = $val['user_id'];
									D('Porder') -> where(array('id' => $val['order_id'], 'order_status' => 2)) -> save(array('tuan_status' => 3, 'order_status' => 3));
									//====================拼团成功通知===========================
									include_once "Baocms/Lib/Net/Wxmesg.class.php";
									$_data_ctsuccess = array(//整体变更
										'url' => "http://" . $_SERVER['HTTP_HOST'] . "/mobile/pintuan/tuan/id/" . $tuan_id . ".html", 
										'topcolor' => '#F55555', 
										'first' => '您参团的商品［' . $Porder['goods_name'] . '］已组团成功，即将发货，请注意查收！', 
										'payprice' => round($order['need_pay'] / 100, 2) . '元', 
										'orderno' => $order['log_id'], 
										'remark' => '点击查看订单详情', 
									);
									$ctsuccess_data = Wxmesg::ctsuccess($_data_ctsuccess);
									$return = Wxmesg::net($uid, 'OPENTM401153728', $ctsuccess_data);
									//结束
									//====================拼团成功通知===============================
								}
							}
							$outArr = array('tstatus' => 300, 'tuan_id' => $tuan_id, );
							echo json_encode($outArr);
							exit ;
						}
					}
					break;
			}
		} else {
			$outArr = array('status' => 11111);
			echo json_encode($outArr);
			exit ;
		}
	}

	public function yes($log_id) {
		$log_id = (int)$log_id;
		$logs = D('Paymentlogs') -> find($log_id);
		switch ($logs['type']) {
			case 'ele' :
				$this -> ele_success('恭喜您支付成功啦！', $logs);
				break;
			case 'goods' :
				if (empty($logs['order_id'])) {
					$this -> success('合并付款成功', U('mcenter/goods/index'));
				} else {
					$this -> goods_success('恭喜您支付成功啦！', $logs);
				}
				break;
			case 'ding' :
				$this -> ding_success('恭喜您支付成功啦！', $logs);
				break;
			case 'booking' :
				$this -> booking_success('恭喜您支付成功啦！', $logs);
				break;
			case 'tuan' :
				$this -> other_success('恭喜您支付成功啦！', $logs);
				break;
			case 'pintuan' :
				$this -> pintuan_success('恭喜您支付成功啦！', $logs);
				break;
			default :
				$this -> success('恭喜您充值成功', U('mcenter/index/index'));
				break;
		}
	}

	public function payment($log_id) {
		if (empty($this -> uid)) {
			header("Location:" . U('passport/login'));
			die ;
		}
		$log_id = (int)$log_id;

		$logs = D('Paymentlogs') -> find($log_id);
		if (empty($logs) || $logs['user_id'] != $this -> uid || $logs['is_paid'] == 1) {
			$this -> error('没有有效的支付记录！');
			die ;
		}
		$this -> assign('button', D('Payment') -> getCode($logs));
		$this -> assign('types', D('Payment') -> getTypes());
		$this -> assign('logs', $logs);
		$this -> assign('paytype', D('Payment') -> getPayments());
		$this -> display();
	}
	//不知道是什么鬼东西
	public function payment2($log_id){
        $log_id = (int) $log_id;
        $this->assign('log_id',$log_id);
        $this->display();
    }

}
