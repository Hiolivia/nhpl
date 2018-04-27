<?php
class DingAction extends CommonAction
{
    private $create_fields = array('shop_id','shop_name','city_id', 'area_id','business_id','addr', 'price', 'deposit', 'mobile', 'tel', 'photo','thumb', 'type', 'lng', 'lat', 'details','orderby');
    private $edit_fields = array('shop_id', 'shop_name','city_id', 'area_id','business_id','addr', 'price', 'deposit', 'mobile', 'tel', 'photo','thumb', 'type', 'lng', 'lat', 'details','orderby');
    public function _initialize(){
        parent::_initialize();
        $getDingType = D('Shopding')->getDingType();
        $this->assign('getDingType', $getDingType);
    }
    public function index(){
        $Shopding = D('Shopding');
        import('ORG.Util.Page');
        $map = array();
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['shop_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($area_id = (int) $this->_param('area_id')) {
            $map['area_id'] = $area_id;
            $this->assign('area_id', $area_id);
        }
        if ($cate_id = (int) $this->_param('cate_id')) {
            $map['cate_id'] = array('IN', D('Shopcate')->getChildren($cate_id));
            $this->assign('cate_id', $cate_id);
        }
        $count = $Shopding->where($map)->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $list = $Shopding->where($map)->order(array('shop_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('areas', D('Area')->fetchAll());
        $this->assign('cates', D('Shopcate')->fetchAll());
        $this->assign('business', D('Business')->fetchAll());
        $this->display();
    }
    public function create()
    {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Shopding');
			
            $type = $this->_post('type', false);
            $type = implode(',', $type);
            $type['cate'] = $type;
			
			//详情增加开始
				$data['details'] = $this->_post('details', 'SecurityEditorHtml');
                if (empty($data['details'])) {
                    $this->baoError('详情不能为空');
                }
                if ($words = D('Sensitive')->checkWords($data['details'])) {
                    $this->baoError('详细内容含有敏感词：' . $words);
                }
			//详情增加结束
			
			$thumb = $this->_param('thumb', false);
            foreach ($thumb as $k => $val) {
                if (empty($val)) {
                    unset($thumb[$k]);
                }
                if (!isImage($val)) {
                    unset($thumb[$k]);
                }
            }
            $data['thumb'] = serialize($thumb);
			
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('ding/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }
    private function createCheck()
    {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['shop_id'] = (int) $data['shop_id'];
        if (empty($data['shop_id'])) {
            $this->baoError('ID不能为空');
        }
        if (!($shop = D('Shop')->find($data['shop_id']))) {
            $this->baoError('商家不存在');
        }
		$data['shop_name'] = $shop['shop_name'];
        $data['city_id'] = $shop['city_id'];
        $data['area_id'] = $shop['area_id'];
        $data['business_id'] = $shop['business_id'];
		
		$data['addr'] = htmlspecialchars($data['addr']);
		$data['price'] = (int) ($data['price'] * 100);
		$data['deposit'] = (int) ($data['deposit'] * 100);
		if (empty($data['deposit'])) {
            $this->baoError('定金不能为空');
        }
		$data['mobile'] = htmlspecialchars($data['mobile']);
		$data['tel'] = htmlspecialchars($data['tel']);
		
		$data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传图片');
        }
       
		$data['type'] = (int) $data['type'];
		
		$data['lng'] = (int) $data['lng'];
        $data['lat'] = (int) $data['lat'];

        $data['orderby'] = (int) $data['orderby'];
		$data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }
    public function edit($shop_id = 0)
    {
        if ($shop_id = (int) $shop_id) {
            $obj = D('Shopding');
            if (!($detail = $obj->find($shop_id))) {
                $this->baoError('请选择要编辑的餐饮商家');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['shop_id'] = $shop_id;
				
                $type = $this->_post('type', false);
				$type = implode(',', $type);
				$type['cate'] = $type;
				//详情增加开始
				$data['details'] = $this->_post('details', 'SecurityEditorHtml');
                if (empty($data['details'])) {
                    $this->baoError('详情不能为空');
                }
                if ($words = D('Sensitive')->checkWords($data['details'])) {
                    $this->baoError('详细内容含有敏感词：' . $words);
                }
			//详情增加结束
				$thumb = $this->_param('thumb', false);
				foreach ($thumb as $k => $val) {
					if (empty($val)) {
						unset($thumb[$k]);
					}
					if (!isImage($val)) {
						unset($thumb[$k]);
					}
				}
				$data['thumb'] = serialize($thumb);
			
			
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('ding/index'));
                }
                $this->baoError('操作失败');
            } else {
                $type = explode(',', $detail['$type']);
                $this->assign('type', $type);
                $this->assign('detail', $obj->_format($detail));
				$thumb = unserialize($detail['thumb']);
                $this->assign('thumb', $thumb);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的餐饮商家');
        }
    }
    private function editCheck(){
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['shop_id'] = (int) $data['shop_id'];
        if (empty($data['shop_id'])) {
            $this->baoError('ID不能为空');
        }
        if (!($shop = D('Shop')->find($data['shop_id']))) {
            $this->baoError('商家不存在');
        }
		$data['shop_name'] = $shop['shop_name'];
        $data['city_id'] = $shop['city_id'];
        $data['area_id'] = $shop['area_id'];
        $data['business_id'] = $shop['business_id'];
		$data['addr'] = htmlspecialchars($data['addr']);
		$data['price'] = (int) ($data['price'] * 100);
		$data['deposit'] = (int) ($data['deposit'] * 100);
		if (empty($data['deposit'])) {
            $this->baoError('定金不能为空');
        }
		$data['mobile'] = htmlspecialchars($data['mobile']);
		$data['tel'] = htmlspecialchars($data['tel']);
		
		$data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传图片');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('图片格式不正确');
        }
		$data['type'] = (int) $data['type'];
		
		$data['lng'] = (int) $data['lng'];
        $data['lat'] = (int) $data['lat'];
        $data['orderby'] = (int) $data['orderby'];
		$data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }
    public function delete($shop_id = 0)
    {
        if (is_numeric($shop_id) && ($shop_id = (int) $shop_id)) {
            $obj = D('Shopding');
            $obj->delete($shop_id);
            $this->baoSuccess('删除成功！', U('ding/index'));
        } else {
            $shop_id = $this->_post('shop_id', false);
            if (is_array($shop_id)) {
                $obj = D('Shopding');
                foreach ($shop_id as $id) {
                    $obj->delete($id);
                }
                $this->baoSuccess('删除成功！', U('ding/index'));
            }
            $this->baoError('请选择要删除的餐饮商家');
        }
    }
    public function opened($shop_id = 0, $type = 'open')
    {
        if (is_numeric($shop_id) && ($shop_id = (int) $shop_id)) {
            $obj = D('Shopding');
            $is_open = 0;
            if ($type == 'open') {
                $is_open = 1;
            }
            $obj->save(array('shop_id' => $shop_id, 'is_open' => $is_open));
            $this->baoSuccess('操作成功！', U('ding/index'));
        }
    }
    public function select(){
        $ele = D('Shopding');
        import('ORG.Util.Page');
        $map = array('audit' => 1);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['shop_name|intro'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $ele->where($map)->count();
        $Page = new Page($count, 10);
        $pager = $Page->show();
        $list = $ele->where($map)->order(array('shop_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $pager);
        $this->display();
    }
}