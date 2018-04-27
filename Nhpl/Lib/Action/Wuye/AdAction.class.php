<?php



class AdAction extends CommonAction {

    public function index() {
        $ad = D('Communityad');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('community_id' => $this->community_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
        }
        $list = $ad->order(array('orderby' => 'asc'))->where($map)->select();
        $this->assign('list',$list);
        $this->display(); // 输出模板   
    }

    public function create() {
        if ($this->isPost()) {
            $data['title'] = htmlspecialchars($_POST['title']);
            if (empty($data['title'])) {
                $this->fengmiMsg('标题不能为空');
            }
            $data['photo'] = htmlspecialchars($_POST['photo']);
            if (empty($data['photo'])) {
                $this->fengmiMsg('请上传广告图');
            }
            if (!isImage($data['photo'])) {
                $this->fengmiMsg('广告图格式不正确');
            }
             $data['link_url'] = htmlspecialchars($_POST['link_url']);
             $data['orderby'] = (int)$_POST['orderby'];
             $data['community_id'] = $this->community_id;
             $data['create_time'] = NOW_TIME;
             $data['create_ip'] = get_client_ip();
            $obj = D('Communityad');
            if ($ad_id = $obj->add($data)) {
                $this->fengmiMsg('添加成功', U('ad/index'));
            }
            $this->fengmiMsg('操作失败！');
        } else {
            $this->display();
        }
    }

    public function edit($ad_id) {
        
        $ad_id = (int)$ad_id;
        if(empty($ad_id)){
            $this->error('广告不存在');
        }
        if(!$detail = D('Communityad')->find($ad_id)){
            $this->error('广告不存在');
        }
        if($detail['community_id'] != $this->community_id){
            $this->error('不能操作他人广告');
        }
        
        if ($this->isPost()) {
            $data['title'] = htmlspecialchars($_POST['title']);
            if (empty($data['title'])) {
                $this->fengmiMsg('标题不能为空');
            }
            $data['photo'] = htmlspecialchars($_POST['photo']);
            if (empty($data['photo'])) {
                $this->fengmiMsg('请上传广告图');
            }
            if (!isImage($data['photo'])) {
                $this->fengmiMsg('广告图格式不正确');
            }
             $data['link_url'] = htmlspecialchars($_POST['link_url']);
             $data['orderby'] = (int)$_POST['orderby'];
             $data['ad_id'] = $ad_id;
            $obj = D('Communityad');
            if (false !== $obj->save($data)) {
                $this->fengmiMsg('编辑成功', U('ad/index'));
            }
            $this->fengmiMsg('操作失败！');
        } else {
            $this->assign('detail',$detail);
            $this->display();
        }
    }
    
    
    
    public function delete() {
        $ad_id = (int) $this->_param('ad_id');
        $obj = D('Communityad');
        $detail = $obj->find($ad_id);
        if (!empty($detail) && $detail['community_id'] == $this->community_id) {
            $obj->delete($ad_id);
			$this->ajaxReturn(array('status' => 'success', 'msg' => '删除成功', U('ad/index')));
            //$this->success('删除成功！', U('ad/index'));
        }
		
	
		
        $this->ajaxReturn(array('status'=>'error','msg'=>'访问错误！'));
    }

}
