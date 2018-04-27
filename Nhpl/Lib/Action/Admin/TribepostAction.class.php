<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TribepostAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
        $this->assign('cates',D('Tribecate')->fetchAll());
    }
    
    
    public function index() {
        $tribepost = D('Tribepost');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'audit' => 1);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $tribepost->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $tribepost->where($map)->order(array('post_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = array();
        foreach($list as $k=>$val){
            $user_ids[$val['user_id']] = $val['user_id'];
        }
        $this->assign('users',D('Users')->itemsByIds($user_ids));
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
    
    public function noaudit() {
        $tribe = D('Tribe');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0, 'audit' => 1);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['tribe_name'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        if ($cate_id = (int) $this->_param('cate_id')) {
            $map['cate_id'] = $cate_id;
            $this->assign('cate_id', $cate_id);
        }
        $count = $tribe->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $tribe->where($map)->order(array('tribe_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }


    public function create($tribe_id) {
        if ($tribe_id = (int) $tribe_id) {
            $obj = D('Tribepost');
            if (!$detail = D('Tribe')->find($tribe_id)) {
                $this->baoError('部落不正确');
            }
            if ($this->isPost()) {
                $data = $this->createCheck();
                $thumb = $this->_param('thumb', false);
                foreach ($thumb as $k => $val) {
                    if (empty($val)) {
                        unset($thumb[$k]);
                    }
                    if (!isImage($val)) {
                        unset($thumb[$k]);
                    }
                }
                $data['tribe_id'] = $tribe_id;
                $data['cate_id'] = $detail['cate_id'];
                if ($post_id = $obj->add($data)) {
                    D('Tribe')->updateCount($tribe_id,'posts');
                    foreach($thumb as $k=>$val){
                        D('Tribepostphoto')->add(array('post_id'=>$post_id,'photo'=>$val));
                    }
                    $this->baoSuccess('操作成功', U('tribepost/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择发帖所属的部落');
        }
    }
    
    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), array('title','details'));
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('话题标题不能为空');
        }
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('话题简介不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('话题简介含有敏感词：' . $words);
        }
        $data['audit'] = 1;
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        return $data;
    }
    
    
    public function edit($post_id = 0) {

        if ($post_id = (int) $post_id) {
            $obj = D('Tribepost');
            if (!$detail = $obj->find($post_id)) {
                $this->baoError('请选择要编辑的话题');
            }
            $tribe = D('Tribe')->find($detail['tribe_id']);
            if ($this->isPost()) {
                $data = $this->editCheck();
                $thumb = $this->_param('thumb', false);
                foreach ($thumb as $k => $val) {
                    if (empty($val)) {
                        unset($thumb[$k]);
                    }
                    if (!isImage($val)) {
                        unset($thumb[$k]);
                    }
                }
                $data['post_id'] = $post_id;
                $data['cate_id'] = $tribe['cate_id'];
                if (false !== $obj->save($data)) {
                    D('Tribepostphoto')->where(array('post_id'=>$post_id))->delete();
                    foreach($thumb as $k=>$val){
                        D('Tribepostphoto')->add(array('post_id'=>$post_id,'photo'=>$val));
                    }
                    $this->baoSuccess('操作成功', U('tribepost/index'));
                }
                $this->baoError('操作失败');
            } else {
                $thumb = D('Tribepostphoto')->where(array('post_id'=>$post_id))->select();
                $this->assign('thumb', $thumb);
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的话题');
        }
    }
    
    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), array('title','details'));
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('话题标题不能为空');
        }
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('话题简介不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('话题简介含有敏感词：' . $words);
        } 
        $data['audit'] = 1;
        return $data;
    }
    
    public function audit($post_id = 0) {
        $obj = D('Tribepost');
        if (is_numeric($post_id) && ($post_id = (int) $post_id)) {
            $obj->save(array('post_id' => $post_id, 'audit' => 1));
            $this->baoSuccess('审核成功！', U('tribepost/index'));
        } else {
            $post_id = $this->_post('post_id', false);
            if (is_array($post_id)) {
                foreach ($post_id as $id) {
                    $obj->save(array('post_id' => $id, 'audit' => 1));
                }
                $this->baoSuccess('审核成功！', U('tribepost/index'));
            }
            $this->baoError('请选择要审核的话题');
        }
    }
    
    
    public function delete($post_id = 0) {
        $obj = D('Tribepost');
        if (is_numeric($post_id) && ($post_id = (int) $post_id)) {
            $obj->save(array('post_id' => $post_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('tribepost/index'));
        } else {
            $post_id = $this->_post('post_id', false);
            if (is_array($post_id)) {
                foreach ($post_id as $id) {
                    $obj->save(array('post_id' => $id, 'closed' => 1));
                }
                $this->baoSuccess('删除成功！', U('tribepost/index'));
            }
            $this->baoError('请选择要删除的话题');
        }
    }
    
}
