<?php
class TribeAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
        $this->assign('cates',D('Tribecate')->fetchAll());
    }
    
    
    public function index() {
        $tribe = D('Tribe');
        import('ORG.Util.Page'); 
        $map = array('closed' => 0, 'audit' => 1);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['tribe_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($cate_id = (int) $this->_param('cate_id')) {
            $map['cate_id'] = $cate_id;
            $this->assign('cate_id', $cate_id);
        }
        $count = $tribe->where($map)->count(); 
        $Page = new Page($count, 25); 
        $show = $Page->show(); 
        $list = $tribe->where($map)->order(array('tribe_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show); 
        $this->display(); 
    }


	//贴吧打赏列表
	public function donate(){
        $Tribedonate = D('Tribedonate');
        import('ORG.Util.Page');
        $map = array();
        if ($user_id = (int) $this->_param('user_id')) {
            $users = D('Users')->find($user_id);
            $this->assign('nickname', $users['nickname']);
            $this->assign('user_id', $user_id);
            $map['user_id'] = $user_id;
        }
        $count = $Tribedonate->where($map)->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $list = $Tribedonate->where($map)->order(array('donate_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$user_ids = array();
        foreach ($list as $k => $val) {
			$user_ids[$val['user_id']] = $val['user_id'];
            $val['create_ip_area'] = $this->ipToArea($val['create_ip']);
            $list[$k] = $val;
        }
		$this->assign('users', D('Users')->itemsByIds($user_ids));
		$this->assign('sum', $sum = $Tribedonate->where($map)->sum('money'));
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
    public function order() {
        $tribe = D('Tribe');
        import('ORG.Util.Page'); 
        $map = array('closed' => 0, 'audit' => 1);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['tribe_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($cate_id = (int) $this->_param('cate_id')) {
            $map['cate_id'] = $cate_id;
            $this->assign('cate_id', $cate_id);
        }
        $count = $tribe->where($map)->count(); 
        $Page = new Page($count, 25); 
        $show = $Page->show(); 
        $list = $tribe->where($map)->order(array('tribe_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); 
        $this->assign('page', $show); 
        $this->display(); 
    }
    

    public function create() {
        $obj = D('Tribe');
        if ($this->isPost()) {
            $data = $this->createCheck();
            if ($tribe_id = $obj->add($data)) {
                $this->baoSuccess('操作成功', U('tribe/index'));
            }
            $this->baoError('操作失败');
        } else {
            $this->assign('detail', $detail);
            $this->display();
        }
       
    }
    
    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), array('tribe_name','cate_id','intro', 'photo','banner','is_hot'));
        $data['tribe_name'] = htmlspecialchars($data['tribe_name']);
        if (empty($data['tribe_name'])) {
            $this->baoError('部落名称不能为空');
        }$data['cate_id'] = (int)$data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('部落分类不能为空');
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        } 
        $data['banner'] = htmlspecialchars($data['banner']);
        if (empty($data['banner'])) {
            $this->baoError('请上传banner图');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('banner图格式不正确');
        } 
        $data['intro'] = SecurityEditorHtml($data['intro']);
        if (empty($data['intro'])) {
            $this->baoError('部落简介不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['intro'])) {
            $this->baoError('部落简介含有敏感词：' . $words);
        } 
        $data['is_hot'] = (int)$data['is_hot'];
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }
    
    
    public function edit($tribe_id = 0) {

        if ($tribe_id = (int) $tribe_id) {
            $obj = D('Tribe');
            if (!$detail = $obj->find($tribe_id)) {
                $this->baoError('请选择要编辑的部落');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['tribe_id'] = $tribe_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('tribe/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的部落');
        }
    }
    
    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), array('tribe_name','cate_id','intro', 'photo','banner','is_hot'));
        $data['tribe_name'] = htmlspecialchars($data['tribe_name']);
        if (empty($data['tribe_name'])) {
            $this->baoError('部落名称不能为空');
        }$data['cate_id'] = (int)$data['cate_id'];
        if (empty($data['cate_id'])) {
            $this->baoError('部落分类不能为空');
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        } 
        $data['banner'] = htmlspecialchars($data['banner']);
        if (empty($data['banner'])) {
            $this->baoError('请上传banner图');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('banner图格式不正确');
        } 
        $data['intro'] = SecurityEditorHtml($data['intro']);
        if (empty($data['intro'])) {
            $this->baoError('部落简介不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['intro'])) {
            $this->baoError('部落简介含有敏感词：' . $words);
        } 
        $data['is_hot'] = (int)$data['is_hot'];
        return $data;
    }
    
    
    public function delete($tribe_id = 0) {
        $obj = D('Tribe');
        if (is_numeric($tribe_id) && ($tribe_id = (int) $tribe_id)) {
            $obj->save(array('tribe_id' => $tribe_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('tribe/index'));
        } else {
            $tribe_id = $this->_post('tribe_id', false);
            if (is_array($tribe_id)) {
                foreach ($tribe_id as $id) {
                    $obj->save(array('tribe_id' => $id, 'closed' => 1));
                }
                $this->baoSuccess('删除成功！', U('tribe/index'));
            }
            $this->baoError('请选择要删除的部落');
        }
    }
    
}
