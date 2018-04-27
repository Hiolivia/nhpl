<?php



class BbsAction extends CommonAction {

    public function index() {
        $bbs = D('Communityposts');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('community_id' => $this->community_id,'closed'=>0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
        }
        $count = $bbs->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $bbs->order(array('post_id' => 'desc'))->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = $last_ids =  array();
        foreach ($list as $k => $val) {
            $last_ids[$val['last_id']] = $val['last_id'];
            $user_ids[$val['user_id']] = $val['user_id'];
        }
        $this->assign('lasts', D('Users')->itemsByIds($last_ids));
        $this->assign('users', D('Users')->itemsByIds($user_ids));
        $this->assign('list', $list);
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }


    public function audit() {
        if (IS_AJAX) {
            $post_id = (int) $_POST['post_id'];
            $obj = D('Communityposts');
            if (empty($post_id)) {
			
                $this->ajaxReturn(array('status' => 'error', 'msg' => '帖子不存在'));
            }
            if (!$detail = $obj->find($post_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '帖子不存在'));
            }
            if ($detail['community_id'] != $this->community_id) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该帖不是您小区的'));
            }
            if(false !== $obj->save(array('post_id'=>$post_id,'audit'=>1))){
                $this->ajaxReturn(array('status' => 'success', 'msg' => '审核成功'));
            }
        }
    }
    
    

    public function delete() {
        if (IS_AJAX) {
            $post_id = (int) $_POST['post_id'];
            $obj = D('Communityposts');
            if (empty($post_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '帖子不存在'));
            }
            if (!$detail = $obj->find($post_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '帖子不存在'));
            }
            if ($detail['community_id'] != $this->community_id) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '该帖不是您小区的'));
            }
            if(false !== $obj->save(array('post_id'=>$post_id,'closed'=>1))){
                $this->ajaxReturn(array('status' => 'success', 'msg' => '删除成功'));
            }
        }
    }
	

	
	
    
}
