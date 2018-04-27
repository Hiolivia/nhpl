<?php
class CouponAction extends CommonAction
{
    public function index()
    {
        $Coupon = D('Coupon');
        $linkArr = array();
        import('ORG.Util.Page');
        // 导入分页类
        $map = array('audit' => 1, 'city_id' => $this->city_id, 'closed' => 0, 'expire_date' => array('EGT', TODAY));
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
            $linkArr['keywrod'] = $map['title'];
        }
        //初始数据
        $cates = D('Shopcate')->fetchAll();
        $cat = (int) $this->_param('cat');
        $cate_id = (int) $this->_param('cate_id');
        if ($cat) {
            if (!empty($cate_id)) {
                $map['cate_id'] = $cate_id;
                $this->seodatas['cate_name'] = $cates[$cate_id]['cate_name'];
                $linkArr['cat'] = $cat;
                $linkArr['cate_id'] = $cate_id;
            } else {
                $catids = D('Shopcate')->getChildren($cat);
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
        $count = $Coupon->where($map)->count();
        // 查询满足要求的总记录数
        $Page = new Page($count, 15);
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();
        // 分页显示输出
        $list = $Coupon->where($map)->order(array('downloads' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $list[$k] = $val;
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list);
        // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show);
        // 赋值分页输出
        $this->assign('cates', $cates);
        $this->assign('count', $count);
        $this->assign('host', __HOST__);
        $this->assign('linkArr', $linkArr);
        $this->display();
        // 输出模板
    }
    public function detail()
    {
        $coupon_id = (int) $this->_get('coupon_id');
        if (empty($coupon_id)) {
            $this->error('该优惠券不存在！');
            die;
        }
        $Coupon = D('Coupon');
        if (!($detail = $Coupon->find($coupon_id))) {
            $this->error('该优惠券不存在！');
            die;
        }
        $url = U('mobile/coupon/detail', array('coupon_id' => $coupon_id));
        $url = __HOST__ . $url;
        $tooken = 'coupon' . $coupon_id;
        $file = baoQrCode($tooken, $url);
        $detail['file'] = $file;
        $Coupon->updateCount($coupon_id, 'views');
        $shop = D('Shop')->find($detail['shop_id']);
        $this->assign('shop', $shop);
        $this->assign('ex', D('Shopdetails')->find($detail['shop_id']));
        $this->assign('detail', $detail);
        $this->seodatas['shop_name'] = $shop['shop_name'];
        $this->seodatas['title'] = $detail['title'];
        $maps = array('closed' => 0, 'shop_id' => $detail['shop_id'], 'audit' => 1);
        $lists = D('Shopbranch')->where($maps)->order(array('orderby' => 'asc'))->select();
        $shop_arr = array('name' => '总店', 'score' => $shop['score'], 'score_num' => $shop['score_num'], 'lng' => $shop['lng'], 'lat' => $shop['lat'], 'telephone' => $shop['tel'], 'addr' => $shop['addr']);
        if (!empty($lists)) {
            array_unshift($lists, $shop_arr);
        } else {
            $lists[] = $shop_arr;
        }
        $counts = count($lists);
        if ($counts % 5 == 0) {
            $num = $counts / 5;
        } else {
            $num = (int) ($counts / 5) + 1;
        }
        $this->assign('count', $counts);
        $this->assign('totalnum', $num);
        $this->assign('lists', $lists);
        $this->display();
        // 输出模板
    }
    public function baoprint()
    {
        $coupon_id = (int) $this->_get('coupon_id');
        if (empty($coupon_id)) {
            $this->error('该优惠券不存在！');
            die;
        }
        $Coupon = D('Coupon');
        if (!($detail = $Coupon->find($coupon_id))) {
            $this->error('该优惠券不存在！');
            die;
        }
        $this->assign('detail', $detail);
        $this->display();
        // 输出模板
    }
    public function download()
    {
        if (empty($this->uid)) {
            $this->ajaxReturn(array('status' => 'login'));
        }
        if (empty($this->member['mobile'])) {
            $this->ajaxReturn(array('status' => 'check_mobile'));
        }
        $coupon_id = (int) $this->_get('coupon_id');
        if (empty($coupon_id)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该优惠券不存在！'));
            die;
        }
        $Coupon = D('Coupon');
        if (!($detail = $Coupon->find($coupon_id))) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该优惠券不存在！'));
            $this->baoError('该优惠券不存在！');
            die;
        }
        if ($detail['expire_date'] < TODAY) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该优惠券已经过期！'));
        }
        if ($detail['num'] <= 0) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '该优惠券已经下载完了！'));
        }
        if ($detail['limit_num']) {
            $count = D('Coupondownload')->where(array('coupon_id' => $coupon_id, 'user_id' => $this->uid))->count();
            if ($count + 1 > $detail['limit_num']) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '您已经超过下载该优惠券的限制了！'));
            }
        }
        $shop = D('Shop')->find($detail['shop_id']);
        $code = D('Coupondownload')->getCode();
        $data = array(
			'user_id' => $this->uid, 
			'shop_id' => $detail['shop_id'], 
			'coupon_id' => $coupon_id, 
			'create_time' => NOW_TIME, 
			'mobile' => $this->member['mobile'], 
			'create_ip' => get_client_ip(), 
			'code' => $code
		);
        if (D('Coupondownload')->add($data)) {
            D('Coupon')->updateCount($coupon_id, 'downloads');
            D('Coupon')->updateCount($coupon_id, 'num', -1);
            //大鱼短信
            if ($this->_CONFIG['sms']['dxapi'] == 'dy') {
                D('Sms')->DySms($this->_CONFIG['site']['sitename'], 'sms_dytz', $this->member['mobile'], array(
					'coupon_title' => $detail['title'], 
					'shop_name' => $shop['shop_name'], 
					'code' => $code, 
					'expire_date' => $detail['expire_date']
				));
            } else {
                D('Sms')->sendSms('sms_coupon_downloads', $this->member['mobile'], array(
					'coupon_title' => $detail['title'], 
					'shop_name' => $shop['shop_name'], 
					'code' => $code, 
					'expire_date' => $detail['expire_date']
				));
            }
            $this->ajaxReturn(array('status' => 'success', 'msg' => '恭喜您下载成功！'));
        }
        $this->ajaxReturn(array('status' => 'error', 'msg' => '下载失败！'));
    }
}