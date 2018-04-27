<?php
class TiebaAction extends CommonAction
{
    protected $sharecates = array();
    public function _initialize()
    {
        parent::_initialize();
        $tieba = (int) $this->_CONFIG['operation']['tieba'];
        if ($tieba == 0) {
            $this->error('此功能已关闭');
            die;
        }
        $Post = D('Post');
        $sharecates = D('Sharecate')->fetchAll();
        foreach ($sharecates as $key => $v) {
            if ($v['cate_id']) {
                $catids = D('Sharecate')->getChildren($v['cate_id']);
                if (!empty($catids)) {
                    $map['cate_id'] = array('IN', $catids);
                } else {
                    $map['cate_id'] = $cat;
                }
            }
            $count = $Post->where($map)->count();
            // 统计当前分类记录
            $sharecates[$key]['count'] = $count;
        }
        $this->assign('sharecates', $sharecates);
        $this->sharecates = D('Sharecate')->fetchAll();
        $this->assign('sharecatess', $this->sharecates);
    }
    public function main()
    {
        $this->display();
        // 输出模板
    }
    public function index(){
        $Post = D('Post');
        $cat = (int) $this->_param('cat');
        $count['post'] = $Post->count();
        $count['reply'] = D('Postreply')->count();
        $ding = $Post->where(array('is_fine' => '1'))->order('create_time desc')->limit(0, 5)->select();
        $this->assign('nextpage', LinkTo('tieba/loaddata', array('cat' => $cat, 't' => NOW_TIME, 'p' => '0000')));
        $this->assign('ding', $ding);
        $this->assign('cat', $cat);
        $this->assign('count', $count);
        $this->display();
        // 输出模板
    }
    public function loaddata(){
        $Post = D('Post');
        import('ORG.Util.Page');
        $map = array('closed' => 0, 'audit' => 1, 'city_id' => $this->city_id);
        $parent_id = 0;
        $cat = (int) $this->_param('cat');
        if ($cat) {
            $catids = D('Sharecate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
                $parent_id = $cat;
            } else {
                $parent_id = $this->sharecates[$cat]['parent_id'];
                $map['cate_id'] = $cat;
            }
            $this->seodatas['cate_name'] = $this->sharecates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);
        $this->assign('parent_id', $parent_id);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $linkArr['order'] = $order = $this->_param('order', 'htmlspecialchars');
        $orderby = array('post_id' => 'desc');
        $cate = $this->sharecates[$cat];
        $this->assign('cate', $cate);
        $this->assign('order', $order);
        $count = $Post->where($map)->count();
        // 查询满足要求的总记录数
        $Page = new Page($count, 10);
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();
        // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Post->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $ids = array();
        foreach ($list as $k => $val) {
            if ($val['user_id']) {
                $ids[$val['user_id']] = $val['user_id'];
            }
            $reply = D('Postreply')->where(array('post_id' => $val['post_id']))->limit(0, 5)->order('reply_id desc')->select();
            foreach ($reply as $i => $arr) {
                $reply[$i]['user'] = D('Users')->find($arr[user_id]);
            }
            $list[$k]['reply'] = $reply;
        }
        $this->assign('users', D('Users')->itemsByIds($ids));
        $this->assign('list', $list);
        // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show);
        // 赋值分页输出
        $this->assign('counts', $count);
        $this->assign('linkArr', $linkArr);
        $this->display();
        // 输出模板
    }
    public function loadreply()
    {
        $post_id = (int) $this->_param('post_id');
        $Postreply = D('Postreply');
        import('ORG.Util.Page');
        // 导入分页类
        // $map = array('post_id' => $post_id);// 不用审核直接显示
        $map = array('audit' => 1, 'post_id' => $post_id);
        // 审核后才显示
        $count = $Postreply->where($map)->count();
        // 查询满足要求的总记录数
        $Page = new Page($count, 10);
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();
        // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Postreply->where($map)->order(array('reply_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $user_ids = array();
        $user_ids[$detail['user_id']] = $detail['user_id'];
        foreach ($list as $k => $val) {
            $user_ids[$val['user_id']] = $val['user_id'];
            $list[$k] = $val;
        }
        if (!empty($user_ids)) {
            $this->assign('users', D('Users')->itemsByIds($user_ids));
        }
        $this->assign('list', $list);
        // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show);
        // 赋值分页输出
        $this->display();
        // 输出模板
    }
    public function detail()
    {
        $post_id = (int) $this->_get('post_id');
        $detail = D('Post')->find($post_id);
        $puser = D('Users')->find($detail['user_id']);
        $detail['nickname'] = $puser['nickname'];
        if (empty($detail) || $detail['audit'] != 1) {
            $this->error('您查看的内容不存在！');
            die;
        }
        D('Post')->updateCount($post_id, 'views');
        $cate = D('Sharecate')->find($detail['cate_id']);
        $this->assign('puser', $puser);
        $this->assign('cate', $cate);
        $this->assign('detail', $detail);
        $this->assign('count', $count);
        $this->seodatas['title'] = $detail['title'];
        $this->assign('nextpage', LinkTo('tieba/loadreply', array('post_id' => $detail['post_id'], 't' => NOW_TIME, 'p' => '0000')));
        $this->display();
        // 输出模板
    }
    public function zan()
    {
        if (empty($this->uid)) {
            echo "-2";
            die;
        }
        $post_id = (int) $this->_get('post_id');
        $detail = D('Post')->find($post_id);
        if (empty($detail) || $detail['audit'] != 1) {
            echo "-3";
        }
        $data = array('post_id' => $post_id, 'user_id' => $this->uid, 'create_ip' => get_client_ip(), 'create_time' => NOW_TIME);
        if (D('Postzan')->checkIsZan($data['post_id'], $data['create_ip'])) {
            echo "-1";
            die;
        } else {
            D('Postzan')->add($data);
            D('Post')->updateCount($post_id, 'zan_num');
            $num = intval($detail['zan_num']) + 1;
            echo $num;
            die;
        }
    }
    public function zans()
    {
        $reply_id = (int) $this->_get('reply_id');
        $detail = D('Postreply')->find($reply_id);
        if (empty($detail)) {
            $this->fengmiError('您查看的内容不存在！');
        }
        D('Postreply')->updateCount($reply_id, 'zan_num');
        $this->fengmiSuccess('恭喜您，点赞成功！', U('tieba/detail', array('post_id' => $detail['post_id'])));
    }
    public function reply($post_id)
    {
        if (empty($this->uid)) {
            $this->error('登录后才能发帖！', U('passport/login'));
        }
        $post_id = (int) $post_id;
        $detail = D('Post')->find($post_id);
        if (empty($detail) || $detail['post_id'] != $post_id) {
            $this->fengmiMsg('没有该帖子');
        }
        if ($this->isPost()) {
            //if ($data['safecode']!=session('safecode')) {			//回帖防灌水安全认证
            //$this->fengmiMsg('不能重复发帖哦，正在为您跳转……',U('tieba/post',U('tieba/detail', array('post_id' => $post_id))));
            //}
            $data = $this->checkReply();
            $data['post_id'] = $post_id;
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $data['audit'] = $this->_CONFIG['site']['replyaudit'];
            //回帖是否免审核
            $obj = D('Postreply');
            if ($obj->add($data)) {
                D('Post')->updateCount($post_id, 'reply_num');
                session('safecode', null);
                $this->fengmiMsg('回帖成功啦！', U('tieba/detail', array('post_id' => $post_id)));
            }
            $this->fengmiMsg('操作失败！');
        } else {
            $this->assign('detail', $detail);
            //生成安全字符串
            $safecode = md5(time());
            session('safecode', $safecode);
            $this->assign('safecode', $safecode);
            $this->display();
        }
    }
    public function checkReply()
    {
        $data = $this->checkFields($this->_post('data', false), array('contents', 'photo', 'safecode'));
        $data['user_id'] = (int) $this->uid;
        $tupian = $this->_post('data');
        $photos = explode(',', $tupian['photo']);
        $photo = $val = '';
        if (!empty($photos)) {
            foreach ($photos as $val) {
                if (isImage($val) && $val != '') {
                    $photo = $photo . '<img src=/attachs/' . $val . '>';
                }
            }
        }
        $data['details'] = $tupian[contents] . $photo;
        //$data['pic'] = $data['photo'];
        $data['contents'] = SecurityEditorHtml($data['contents'] . $photo);
        if (empty($data['contents'])) {
            $this->fengmiMsg('内容不能为空！');
        }
        if ($words = D('Sensitive')->checkWords($data['contents'])) {
            $this->fengmiMsg('详细内容含有敏感词：' . $words);
        }
        return $data;
    }
    public function post()
    {
        if (empty($this->uid)) {
            $this->error('登录后才能发帖！', U('passport/login'));
        }
        $cat = (int) $this->_param('cat');
        foreach ($this->sharecates as $value) {
            if ($value['cate_id'] == $cat) {
                $cate = $value;
            }
        }
        if ($this->isPost()) {
            $data = $this->postCheck();
            $obj = D('Post');
            $data['city_id'] = $this->city_id;
            //小灰灰自己添加的
            $data['audit'] = $this->_CONFIG['site']['postaudit'];
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $data['cate_id'] = $cate['cate_id'];
            $tupian = $this->_post('data');
            $photos = $this->_post('photos', false);
            $photo = $val = '';
            if (!empty($photos)) {
                foreach ($photos as $val) {
                    if (isImage($val) && $val != '') {
                        $photo = $photo . '<img src=/attachs/' . $val . '>';
                    }
                }
            }
            $photo1 = $val1 = '';
            if (!empty($photos)) {
                foreach ($photos as $val1) {
                    if (isImage($val1) && $val1 != '') {
                        $photo1 = $photo1 . ',' . $val1;
                    }
                }
            }
            $data['pic'] = ltrim($photo1, ',');
            $data['details'] = $tupian[contents] . $photo;
            if ($data['safecode'] != session('safecode')) {
                //发帖防灌水安全认证
                $this->fengmiMsg('不能重复发帖哦，正在为您跳转……', U('tieba/post'));
            }
            if (empty($data['cate_id'])) {
                $this->fengmiMsg('分类不能为空');
            }
            $last = $obj->add($data);
            if ($last) {
                session('safecode', null);
                $this->fengmiMsg('发帖成功啦！', U('tieba/index'));
            }
            $this->fengmiMsg('操作失败！');
        } else {
            $this->assign('cate', $cate);
            //生成安全字符串
            $safecode = md5(time());
            session('safecode', $safecode);
            $this->assign('safecode', $safecode);
            $this->display();
        }
    }
    private function postCheck()
    {
        $data = $this->checkFields($this->_post('data', false), array('title', 'contents', 'safecode'));
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title']) || $data['title'] == '标题') {
            $this->fengmiMsg('标题不能为空');
        }
        $data['user_id'] = (int) $this->uid;
        $data['contents'] = SecurityEditorHtml($data['contents']);
        if (empty($data['contents'])) {
            $this->fengmiMsg('详细内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['contents'])) {
            $this->fengmiMsg('详细内容含有敏感词：' . $words);
        }
        return $data;
    }
}