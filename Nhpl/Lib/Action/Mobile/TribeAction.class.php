<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class TribeAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
		$this->assign('cates',D('Tribecate')->fetchAll());

    }
    
    public function index(){
        $linkArr = array();
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        $this->assign('keyword', $keyword);
        $linkArr['keyword'] = $keyword;
        
        $tribe_id = (int) $this->_param('tribe_id');
        $this->assign('tribe_id', $tribe_id);
        $linkArr['tribe_id'] = $tribe_id;
        
        $this->assign('nextpage', LinkTo('tribe/loaddata',$linkArr,array('t' => NOW_TIME,'p' => '0000')));
        $this->assign('linkArr',$linkArr);
        $this->mobile_title = '部落首页';
		$this->display();
	}
    
    public function loaddata(){
        $tribepost = D('Tribepost');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('audit' => 1, 'closed' => 0);
        if($tribe_id = (int)$this->_param('tribe_id')){
            $map['tribe_id'] = $tribe_id;
            $this->assign('tribe_id',$tribe_id);
        }
        
        $count = $tribepost->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $tribepost->where($map)->order(array('last_time'=>'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $tribe_ids = $post_ids = $user_ids = array();
        foreach($list as $k=>$val){
            $tribe_ids[$val['tribe_id']] = $val['tribe_id'];
            $post_ids[$val['post_id']] = $val['post_id'];
            $user_ids[$val['user_id']] = $val['user_id'];
        }
        $this->assign('tribes',D('Tribe')->itemsByIds($tribe_ids));
        $this->assign('users',D('Users')->itemsByIds($user_ids));
        $pics = D('Tribepostphoto')->where(array('post_id'=>array('IN',$post_ids)))->select();
        foreach($list as $k=>$val){
            foreach($pics as $kk=>$v){
                if($val['post_id'] == $v['post_id']){
                    $list[$k]['pics'][] = $v['photo'];
                }
            }
        }
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出 
		$this->display();
    }

    
    public function postdetail($post_id){
        if(!$post_id = (int)$post_id){
            $this->error('话题不存在');
        }elseif(!$detail = D('Tribepost')->find($post_id)){
            $this->error('话题不存在');
        }elseif($detail['closed'] != 0||$detail['audit'] !=1){
            $this->error('话题不存在');
        }else{
            //详情
            D('Tribepost')->updateCount($post_id,'views');
            $pics = D('Tribepostphoto')->where(array('post_id'=>$post_id))->select();
            $this->assign('pics',$pics);
            if (!$res = D('Tribepostzan')->where(array('create_ip' => get_client_ip(), 'post_id' => $post_id))->find()) {
                $detail['is_zan'] = 0;
            } else {
                $detail['is_zan'] = 1;
            }
            //话题推荐
            $tui_list = D('Tribepost')->where(array('tribe_id'=>$detail['tribe_id'],'post_id'=>array('NEQ',$post_id)))->order(array('post_id'=>'desc'))->limit(4)->select();
            $this->assign('tui_list',$tui_list);

            $this->assign('auth',D('Users')->find($detail['user_id']));
            $this->assign('tribe',D('Tribe')->find($detail['tribe_id']));
            $this->assign('nextpage', LinkTo('tribe/postload',array('post_id'=>$post_id,'t' => NOW_TIME,'p' => '0000')));
            $this->assign('detail',$detail);
            $this->display();
        }
    }

    public function postload(){
            //回复的帖子
            import('ORG.Util.Page'); // 导入分页类
            $post_id = (int)$this->_param('post_id');
            $reply_list = D('Tribepostcomments')->where(array('post_id'=>$post_id,'type'=>array('IN',array(1,2))))->order(array('comment_id'=>'desc'))->select();
            $user_idss = $comment_idss = array();
            foreach($reply_list as $k=>$val){
                $user_idss[$val['user_id']] = $val['user_id'];
                $comment_idss[$val['comment_id']] = $val['comment_id'];
            }
            $userss = D('Users')->itemsByIds($user_idss);
            $reply_picss =  D('Tribecommentsphoto')->where(array('comment_id'=>array('IN',$comment_idss)))->select();
            foreach($reply_list as $k=>$val){
                $reply_list[$k]['users'] = $userss[$val['user_id']];
                foreach($reply_picss as $kk=>$v){
                    if($val['comment_id'] == $v['comment_id']){
                        $reply_list[$k]['pics'][] = $v; 
                    }
                }
            }
            $lists = D('Tribepostcomments')->where(array('post_id'=>$post_id,'type'=>array('IN',array(0,2))))->order(array('comment_id'=>'asc'))->select();
            $count = count($lists);  // 查询满足要求的总记录数 
            $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show(); // 分页显示输出  
            $list = array_slice($lists, $Page->firstRow, $Page->listRows);
            //dump(D('Tribepostcomments')->getLastSql());die;
            foreach($list as $k=>$val){
                foreach($reply_list as $kk=>$v){
                    if($v['reply_comment_id'] == $val['comment_id']){
                        $list[$k]['replys'][] = $v;
                    }
                }
            }
           
            $user_ids = $comment_ids = array();
            $a = 2;
            foreach ($list as $k => $val) {
                if (!empty($val['user_id'])) {
                    $user_ids[$val['user_id']] = $val['user_id'];
                }
                if($val['comment_id']){
                    $comment_ids[$val['comment_id']] = $val['comment_id'];
                }
                $list[$k]['floor'] = $a;
                $a++;
            }
            $reply_pics = D('Tribecommentsphoto')->where(array('comment_id'=>array('IN',$comment_ids)))->select();
           foreach($list as $k=>$val){
              foreach($reply_pics as $kk=>$v){
                    if($val['comment_id'] == $v['comment_id']){
                        $list[$k]['pics'][] = $v; 
                    }
                }
           }
            $this->assign('userss',$userss);
            $this->assign('users', D('Users')->itemsByIds($user_ids));
            $this->assign('list',$list);
            $this->assign('page', $show); // 赋值分页输出 
            $this->display();
    }

    

    public function lists(){
        if($cate_id = (int)$this->_param('cate_id')){
            $this->assign('cate_id',$cate_id);
        }
        $this->assign('nextpage', LinkTo('tribe/listsload',array('cate_id'=>$cate_id,'t' => NOW_TIME,'p' => '0000')));
		$this->display();
	}
    
    
    public function listsload(){
        $tribe = D('Tribe');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0);
        if($cate_id = (int)$this->_param('cate_id')){
            $map['cate_id'] = $cate_id;
            $this->assign('cate_id',$cate_id);
        }
        
        $count = $tribe->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $tribe->where($map)->order(array('tribe_id'=>'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $result = D('Tribecollect')->where(array('user_id'=>$this->uid))->select();
        
        foreach($list as $k=>$val){
            foreach($result as $kk=>$v){
                if($val['tribe_id'] == $v['tribe_id']){
                    $list[$k]['collect'] = 1;
                }
            }
        }
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出 
		$this->display();
    }

    public function attent(){
        if (empty($this->uid)) {
            AppJump();
        }
        $this->assign('nextpage', LinkTo('tribe/attentload',array('t' => NOW_TIME,'p' => '0000')));
		$this->display();
	}
    
    
    public function attentload(){
        $tribecollect = D('Tribecollect');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $tribecollect->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $tribecollect->where($map)->order(array('tribe_id'=>'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        $tribe_ids = array();
        foreach($list as $k=>$val){
            $tribe_ids[$val['tribe_id']] = $val['tribe_id'];
        }
        $this->assign('tribes',D('Tribe')->itemsByIds($tribe_ids));
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出 
		$this->display();
    }
    
    public function collect() {
        if (empty($this->uid)) {
            AppJump();
        }
        $tribe_id = (int) $this->_get('tribe_id');
        if (!$detail = D('Tribe')->find($tribe_id)) {
            $this->error('没有该部落');
        }
        if ($detail['closed']) {
            $this->error('该部落已经被删除');
        }
        if (D('Tribecollect')->check($tribe_id, $this->uid)) {
            if(D('Tribecollect')->where(array('tribe_id'=>$tribe_id,'user_id'=>$this->uid))->delete()){
                D('Tribe')->updateCount($tribe_id,'fans',-1);
                $this->success('取消关注成功！',U('tribe/lists',array('cate_id'=>$detail['cate_id'])));
            }
            $this->error('取消失败！');
        }else{
            $data = array(
                'tribe_id' => $tribe_id,
                'user_id' => $this->uid,
            );
            if (D('Tribecollect')->add($data)) {
                D('Tribe')->updateCount($tribe_id,'fans');
                $this->success('恭喜您关注成功！',U('tribe/detail',array('tribe_id'=>$tribe_id)));
            }
            $this->error('关注失败！');
        }
    }
    
    public function detail($tribe_id){
        if(!$tribe_id = (int)$tribe_id){
            $this->error('部落不存在');
        }elseif(!$detail = D('Tribe')->find($tribe_id)){
            $this->error('部落不存在');
        }elseif($detail['closed'] != 0){
            $this->error('该部落已被删除');
        }else{
            $tribepost = D('Tribepost');
            if($order = (int)$this->_param('order')){
                $this->assign('order',$order);
            }
            $count = $tribepost->where(array('audit'=>1,'closed'=>0,'tribe_id'=>$tribe_id))->count(); // 查询满足要求的总记录数 
            $this->assign('count',$count);
            if($res = D('Tribecollect')->where(array('tribe_id'=>$tribe_id,'user_id'=>$this->uid))->find()){
                $detail['collect'] = 1;
            }
            $collect = D('Tribecollect')->where(array('user_id'=>$this->uid))->select();
            $tr_ids = array();
            foreach ($collect as $k=>$val){
                $tr_ids[] = $val['tribe_id'];
            }            
            $tribes = D('Tribe')->where(array('cate_id'=>$detail['cate_id'],'closed'=>0,'tribe_id'=>array('NOTIN',$tr_ids)))->limit(3)->select();
            $this->assign('tribes',$tribes);
            $this->assign('detail',$detail);
            $this->assign('nextpage', LinkTo('tribe/load',array('tribe_id'=>$tribe_id,'order'=>$order,'t' => NOW_TIME,'p' => '0000')));
            $this->display();
        }
	}
    
    public function load(){
        $tribepost = D('Tribepost');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0,'audit'=>1);
        if($tribe_id = (int)$this->_param('tribe_id')){
            $map['tribe_id'] = $tribe_id;
        }
        if($order = (int)$this->_param('order')){
            if($order == 2){
                $orderby = array('post_id'=>'desc');
            }else{
                $orderby = array('last_time'=>'desc');
            }
        }else{
            $orderby = array('last_time'=>'desc');
        }
        $this->assign('order',$order);
        $count = $tribepost->where($map)->count(); // 查询满足要求的总记录数 
        $this->assign('count',$count);
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $tribepost->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $post_ids = $user_ids = array();
        foreach($list as $k=>$val){
            $post_ids[$val['post_id']] = $val['post_id'];
            $user_ids[$val['user_id']] = $val['user_id'];
        }
        $this->assign('users',D('Users')->itemsByIds($user_ids));
        $pics = D('Tribepostphoto')->where(array('post_id'=>array('IN',$post_ids)))->select();
        foreach($list as $k=>$val){
            foreach($pics as $kk=>$v){
                if($val['post_id'] == $v['post_id']){
                    $list[$k]['pics'][] = $v['photo'];
                }
            }
        }
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出 
        $this->display();
    }

    public function fabu($tribe_id){
        if (empty($this->uid)) {
            AppJump();
        }
        if(!$tribe_id = (int)$tribe_id){
            $this->error('部落不存在');
        }elseif(!$detail = D('Tribe')->find($tribe_id)){
            $this->error('部落不存在');
        }elseif($detail['closed'] != 0){
            $this->error('部落已被删除');
        }else{
            if($this->isPost()){
                $data['title'] = htmlspecialchars($this->_param('title'));
                if(empty($data['title'])){
                    $this->baoError('标题不能为空');
                }
                if ($words = D('Sensitive')->checkWords($data['title'])) {
                    $this->baoError('标题含有敏感词：' . $words);
                } 
                $data['details'] = htmlspecialchars($this->_param('details'));
                if(empty($data['details'])){
                    $this->baoError('详情不能为空');
                }
                if ($words2 = D('Sensitive')->checkWords($data['details'])) {
                    $this->baoError('详情含有敏感词：' . $words2);
                } 
                $photos = $this->_param('photos',false);
                $data['user_id'] = $this->uid;
                $data['cate_id'] = $detail['cate_id'];
                $data['tribe_id'] = $tribe_id;
                $data['create_time'] = NOW_TIME;
                $data['last_id'] = $this->uid;
                $data['last_time'] = NOW_TIME;
                $data['create_ip'] = get_client_ip();
                $data['audit'] = $this->_CONFIG['site']['tribeaudit'];
                if($post_id = D('Tribepost')->add($data)){
                    $local = array();
                    foreach ($photos as $val) {
                        if (isImage($val))
                            $local[] = $val;
                    }
                    if (!empty($local)) {
                        D('Tribepostphoto')->upload($post_id, $local);
                    }
                    D('Tribe')->updateCount($tribe_id,'posts');
                    $this->baoSuccess('恭喜您发帖成功',U('tribe/detail',array('tribe_id'=>$tribe_id)));
                }else{
                    $this->baoError('发帖失败');
                }
            }else{
                $this->assign('detail',$detail);
                $this->display();
            }
        }
	}
    
    public function reply($post_id,$comment_id=0){
        if (empty($this->uid)) {
            AppJump();
        }
        
        if(!$post_id = (int)$post_id){
            $this->error('该话题不存在');
        }elseif(!$post = D('Tribepost')->find($post_id)){
            $this->error('该话题不存在');
        }elseif($post['audit'] !=1||$post['closed']!=0){
            $this->error('该话题不存在');
        }else{
            if($comment_id = (int)$comment_id){
                if(!$detail = D('Tribepostcomments')->find($comment_id)){
                    $this->error('该评论不存在');
                }elseif($detail['closed']!=0){
                    $this->error('该评论不存在');
                }
            }
            if ($this->isPost()) {
                    $data['contents'] = htmlspecialchars($this->_param('contents'));
                    if(empty($data['contents'])){
                        $this->baoSuccess('回复内容不能为空');
                    }
                    if ($words = D('Sensitive')->checkWords($data['contents'])) {
                        $this->baoSuccess('回复内容含有敏感词：' . $words);
                    }
                    if($com_id = (int)$this->_param('comment_id')){
                        
                        $data['reply_comment_id'] = $com_id;
                        $data['type'] = 1;
                    }
                    $data['post_id'] = $post_id;
                    $data['reply_user_id'] = (int) $this->_param('reply_user_id');
                    $data['user_id'] = $this->uid;
                    $data['create_time'] = NOW_TIME;
                    $data['create_ip'] = get_client_ip();
                    $photos = $this->_param('photos',false);
                    if($cid = D('Tribepostcomments')->add($data)){
                        if($photos){
                            foreach($photos as $k=>$val){
                                D('Tribecommentsphoto')->add(array('comment_id'=>$cid,'photo'=>$val));
                            }
                        }
                        D('Tribepost')->updateCount($post_id, 'reply_num');
                        D('Tribepost')->save(array('post_id' => $post_id, 'last_id' => $this->uid, 'last_time' => $data['create_time']));
                        $this->baoSuccess('回复成功',U('tribe/postdetail',array('post_id'=>$post_id)));
                    }else{
                        $this->baoError('回复失败');
                    }
            }else{
                $this->assign('comment_id',$comment_id);
                $this->assign('post_id',$post_id);
                $this->display();
            }
        }
    }
    
    
    
    public function zan() {
        if (IS_AJAX) {
            $post_id = (int) $_POST['post_id'];
            if (empty($post_id)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '话题不存在'));
            }
            $user_id = $this->uid;
            if ($res = D('Tribepostzan')->where(array('post_id' => $post_id, 'create_ip' => get_client_ip()))->find()) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '您已经点过赞了'));
            } else {
                if (D('Tribepostzan')->add(array('post_id' => $post_id, 'user_id' => $user_id, 'create_time' => NOW_TIME, 'create_ip' => get_client_ip()))) {
                    D('Tribepost')->updateCount($post_id, 'zan_num');
                    $this->ajaxReturn(array('status' => 'success', 'msg' => '点赞成功'));
                } else {
                    $this->ajaxReturn(array('status' => 'error', 'msg' => '点赞失败'));
                }
            }
        }
    }
	public function photo($tribe_id){
        if(!$tribe_id = (int)$tribe_id){
             $this->error('部落不存在');
        }
        $map = array('audit'=>1,'closed'=>0,'tribe_id'=>$tribe_id);
        $tribe = D('Tribepost')->where($map)->select();
        if(!$tribe){
            $this->error('部落不存在');
        }
        foreach ($tribe as $k => $v) {
            if(!$post_ids){
                $post_ids = $v['post_id'];
            }else{
                $post_ids = $post_ids.','.$v['post_id'];
            }
        }
        //$maps['post_id']=array('in',array($post_ids));
		//print_r($maps['post_id']);
        //$lists = D('Tribepostphoto')->where($maps)->select();
		$lists = D('Tribepostphoto')->getbypost_ids($post_ids);
		import('ORG.Util.Page'); // 导入分页类
        $count = count($lists);
        $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出  
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = array_slice($lists, $Page->firstRow, $Page->listRows);
        $this->assign('photos',$list);
		//print_r($list);die;
        $this->display();

	}
    

}
