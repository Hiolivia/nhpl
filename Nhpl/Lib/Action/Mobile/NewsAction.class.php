<?php
class NewsAction extends CommonAction {
	protected function _initialize() {
        parent::_initialize();
		$news = (int)$this->_CONFIG['operation']['news'];
		if ($news == 0) {
				$this->error('此功能已关闭');
				die;
		 }
		 
		$Article = D('Article');
		$articlecates = D('Articlecate')->fetchAll();
	    foreach ($articlecates as $key => $v) {
           if ($v['cate_id']) {
            $catids = D('Articlecate')->getChildren($v['cate_id']);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
        }
              $count = $Article->where($map)->count(); // 统计当前分类记录
              $articlecates[$key]['count'] = $count;
        }
        $this->assign('articlecates',$articlecates);
		//结束
		
       }
		
		
	
    public function index() {	
		$keyword = $this->_param('keyword', 'htmlspecialchars');//取得$keyword
        $this->assign('keyword', $keyword);	//赋值给搜索框
		
        $cat = ( integer )$this->_param( "cat" );
        $this->assign( "cat", $cat );
        $linkArr['cat'] = $cat;
		
		$order = $this->_param('order','htmlspecialchars');
        $this->assign('order', $order);
        $linkArr['order'] = $order;
		
		$cates = D('Articlecate')->fetchAll();
		$this->assign('cates', $cates);
		
		$this->assign('nextpage', LinkTo('news/load',$linkArr, array('cat' => $cat,'t' => NOW_TIME,'keyword' => $keyword,'order' => $order, 'p' => '0000')));
        $this->assign( "linkArr", $linkArr );
		$this->display(); // 输出模板
    }
	 public function load() {
		$Article = D('Article');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('city_id' => $this->city_id,'closed' => 0,'audit' => 1);
		
		$cat = (int) $this->_param('cat');
        $cates = D('Weidiancate')->fetchAll();
        if ($cates[$cat]) {
            $catids = D('Weidiancate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
            $this->assign('parent_id', $cates[$cat]['parent_id'] == 0 ? $cates[$cat]['cate_id'] : $cates[$cat]['parent_id']);
            $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);
		//微店二级分类结束
		

        $order = (int) $this->_param('order');
        switch ($order) {
           
            case 2:
                $orderby = array('views' => 'desc');
                break;
            default:
                $orderby = array('article_id' => 'desc');
                break;
        }

        $count = $Article->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 8); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		
        $list = $Article->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
		 }

    public function detail($article_id = 0) {

        if ($article_id = (int) $article_id) {
            $obj = D('Article');
            if (!$detail = $obj->find($article_id)) {
                $this->error('没有该文章');
            }
			if ($detail['closed'] != 0 ) {
            $this->error('该文章不存在');
             }
		
		
            $cates = D('Articlecate')->fetchAll();
            $obj->updateCount($article_id, 'views');
            $this->assign('detail', $detail);
			
			//回复列表
			import('ORG.Util.Page'); // 导入分页类
			$count =  D('Articlecomment')->where(array('post_id'=>$article_id,'parent_id'=>0))->count(); //获取评论总数
			$Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
			$show = $Page->show(); // 分页显示输出
			$this->assign('count',$count);
			$list=array();
			$list=$this->getCommlist($article_id,0,$Page->firstRow,$Page->listRows);//获取评论列表
			$this->assign("list",$list);
			$this->assign('page', $show); // 赋值分页输出
			$user_ids = $post_ids = array();
			foreach ($list as $k => $val) {
				if (!empty($val['user_id'])) {
					$user_ids[$val['user_id']] = $val['user_id'];
				}
			}
			$this->assign('users', D('Users')->itemsByIds($user_ids));

            $this->assign('parent_id', D('Articlecate')->getParentsId($detail['cate_id']));
            $this->assign('cates', $cates);
            $this->assign('cate',$cates[$detail['cate_id']]);
            $this->seodatas['title'] = $detail['title'];
            $this->seodatas['cate_name'] = $cates[$detail['cate_id']];
            $this->seodatas['keywords'] = $detail['keywords'];
            $this->seodatas['desc'] = $detail['desc'];
			
			
			$rands = D("Article")->rands();

		    $this->assign("rands", $rands);
		
		
            $this->display();
        } else {
            $this->error('没有该文章');
        }
    }

    public function system() {
        $content_id = (int) $this->_get('content_id');
        if (empty($content_id)) {
            $this->error('该内容不存在');
            die;
        }
        $contents = D('Systemcontent')->fetchAll();
        if (!$contents[$content_id]) {
            $this->error('该内容不存在');
            die;
        }
        $this->assign('detail', $contents[$content_id]);
        $this->assign('contents', $contents);
        $this->assign('content_id', $content_id);
        $this->seodatas['title'] = $contents[$content_id]['title'];
        $this->display();
    }
	
	
    public function cate() {
		$cates = D('Articlecate')->fetchAll();
        $Article = D('Article');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('audit' => 1,'closed' => 0);
        $cat = (int) $this->_param('cat');
        $cates = D('Articlecate')->fetchAll();
        if ($cates[$cat]) {
            $catids = D('Articlecate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
            $this->assign('parent_id', $cates[$cat]['parent_id'] == 0 ? $cates[$cat]['cate_id'] : $cates[$cat]['parent_id']);
            $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);

        $order = (int) $this->_param('order');
        switch ($order) {
           
            case 2:
                $orderby = array('views' => 'desc');
                break;
            default:
                $orderby = array('article_id' => 'desc');
                break;
        }
		
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }

		$this->assign('cate',$cates[$cat]);
        $count = $Article->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Article->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('cates', $cates);
		$this->assign('nextpage', LinkTo('news/loaddata', array('cat' => $cat, 't' => NOW_TIME, 'p' => '0000')));
        $this->display(); // 输出模板
    }
	
	
    public function loaddata() {
		$cates = D('Articlecate')->fetchAll();
        $Article = D('Article');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('audit' => 1,'closed' => 0);
        $cat = (int) $this->_param('cat');
        $cates = D('Articlecate')->fetchAll();
        if ($cates[$cat]) {
            $catids = D('Articlecate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
            $this->assign('parent_id', $cates[$cat]['parent_id'] == 0 ? $cates[$cat]['cate_id'] : $cates[$cat]['parent_id']);
            $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);

        $order = (int) $this->_param('order');
        switch ($order) {
           
            case 2:
                $orderby = array('views' => 'desc');
                break;
            default:
                $orderby = array('article_id' => 'desc');
                break;
        }

		$this->assign('cate',$cates[$cat]);
        $count = $Article->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
		
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
		
        $list = $Article->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('cates', $cates);
        $this->display(); // 输出模板
    }
	
    public function zans() {
        $comment_id = (int) $this->_get('comment_id');
        $detail = D('Articlecomment')->find($comment_id);
        if (empty($detail)) {
            $this->fengmiMsg('评论已删除');
        }
		$zan = D('Articlecomment')->where(array('comment_id'=>$comment_id,'user_id'=>$this->uid))->find();
		if(empty($zan)){
			D('Articlecomment')->updateCount($comment_id, 'zan');
			$this->fengmiMsg('点赞成功',U('news/detail', array('article_id' => $detail['post_id'])));
		}else{
			$this->fengmiMsg('不能赞自己');	
		}
    }
	public function cai() {
        $comment_id = (int) $this->_get('comment_id');
        $detail = D('Articlecomment')->find($comment_id);
        if (empty($detail)) {
            $this->fengmiMsg('评论已删除');
        }
		$cai = D('Articlecomment')->where(array('comment_id'=>$comment_id,'user_id'=>$this->uid))->find();
		if(empty($cai)){
			D('Articlecomment')->updateCount($comment_id, 'cai');
			$this->fengmiMsg('踩成功',U('news/detail', array('article_id' => $detail['post_id'])));
		}else{
			$this->fengmiMsg('不能踩自己');	
		}
    }
	//点赞文章
	public function zan() {
        $article_id = (int) $this->_get('article_id');
        $detail = D('Article')->find($article_id);
        if (empty($detail)) {
            $this->fengmiMsg('该文章已删除');
        }
		
        D('Article')->updateCount($article_id, 'zan');
	    $this->fengmiMsg('点赞成功',U('news/detail', array('article_id' => $detail['article_id'])));
    }
	
	
	public function post(){
        if (!$this->uid) {
            $this->error('登录状态失效!', U('passport/login'));
        }
		$data = $this->checkFields($this->_post('data', false), array('post_id', 'parent_id', 'audit','content'));
		if (empty($data['content'])) {
			$this->error('评论内容不能为空');
		}
		if (empty($data['post_id'])) {
			$this->error('文章编号不正确');
		}
		if (!$detail = D('Article')->find($data['post_id'])) {
			$this->error('没有该文章');
		}

		
		$data['nickname'] = $this->member['nickname'];
		$data['user_id'] = $this-> uid;
		$data['zan'] =0;
		$data['audit'] = $this->_CONFIG['site']['article_reply_audit'];//评论是免审核
		$data['create_time'] = NOW_TIME;
		$data['create_ip'] = get_client_ip();
		if (D('Articlecomment')->add($data)) {
			$this->success('回复成功！', U('news/detail', array('article_id' => $data['post_id'])));
		}
	}
	
	
    public function replay($article_id = 0) {
        if ($article_id = (int) $article_id) {
            $obj = D('Article');
            if (!$detail = $obj->find($article_id)) {
                $this->error('没有该文章');
            }
			if ($detail['closed'] != 0 ) {
            $this->error('该文章已删除');
             }

            $cates = D('Articlecate')->fetchAll();
            $this->assign('detail', $detail);
			$this->assign('nextpage', LinkTo('news/loadreplay', array('article_id' => $article_id, 't' => NOW_TIME, 'p' => '0000')));
            $this->display();
        } else {
            $this->error('没有该文章');
        }
    }
	 public function donate(){
		if (empty($this->uid)) {
            $this->ajaxReturn(array('status'=>'success','msg'=>'请登录后操作', U('passport/login')));
        }
        $article_id = I('article_id', 0, 'trim,intval');
		$money = (I('money', 0, 'trim,intval'))*100;
		
        $detail = D('Article')->find($article_id);
        if (empty($detail)) {
			$this->ajaxReturn(array('status'=>'error','msg'=>'您打赏的内容不存在'));
        }
		
        if ($money > $this->member['money'] || $this->member['money'] == 0){
             $this->ajaxReturn(array('status'=>'success','msg'=>'您余额不足', U('member/money/money')));
        }else{
			if(!empty($money)){
					$data['article_id'] = $article_id;
					$data['city_id'] = $this->city_id;
					$data['user_id'] = $this->uid;
					$data['money'] = $money;
					$data['create_time'] = NOW_TIME;
					$data['create_ip'] = get_client_ip();
					D('Articledonate')->add($data);
					D('Users')->addMoney($this->uid, -$money ,'打赏文章ID：'.$article_id.'花费金额');
					D('Article')->updateCount($article_id, 'donate_num');
					$this->ajaxReturn(array('status'=>'success','msg'=>'恭喜您，打赏成功！', U('news/detail', array('article_id' => $detail['post_id']))));
				}else{
					$this->ajaxReturn(array('status'=>'error','msg'=>'操作失败'));	
				}
			$this->ajaxReturn(array('status'=>'error','msg'=>'参数错误'));		
		}
        
    }
	
    public function loadreplay() {
		$article_id = (int) $this->_param('article_id');
	
        if ($article_id = (int) $article_id) {
            $obj = D('Article');
            if (!$detail = $obj->find($article_id)) {
                $this->error('没有该文章');
            }
			if ($detail['closed'] != 0 ) {
            $this->error('该文章已删除');
             }

            $this->assign('detail', $detail);
			
			//回复列表
			import('ORG.Util.Page'); // 导入分页类
			$count =  D('Articlecomment')->where(array('post_id'=>$article_id,'parent_id'=>0))->count(); //获取评论总数
			$Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
			$show = $Page->show(); // 分页显示输出
			$this->assign('count',$count);
			
			$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
			$p = $_GET[$var];
			if ($Page->totalPages < $p) {
				die('0');
			}
			
			$list=$this->getCommlist($article_id,0,$Page->firstRow,$Page->listRows);//获取评论列表
			
			$user_ids = $post_ids = array();
			foreach ($list as $k => $val) {
				if (!empty($val['user_id'])) {
					$user_ids[$val['user_id']] = $val['user_id'];
				}
			}
			$this->assign('users', D('Users')->itemsByIds($user_ids));
			
			$this->assign("list",$list);
			$this->assign('page', $show); // 赋值分页输出
            $this->display();
        } else {
            $this->error('没有该文章');
        }
    }
	

    /**
	*递归获取评论列表
    */
    protected function getCommlist($post_id,$parent_id = 0,$start,$end,&$result = array()){
		$map = array();
		$map['post_id'] = $post_id;
		$map['parent_id'] = $parent_id;
		if($parent_id != 0){
			$arr = D('Articlecomment')->where($map)->order("zan desc")->select();
		}else{
			$arr = D('Articlecomment')->where($map)->order("zan desc")->limit($start.','.$end)->select();
		}
    	
    	if(empty($arr)){
    		return array();
    	}
    	foreach ($arr as $cm) {  
    		$thisArr=&$result[];
    		$cm["children"] = $this->getCommlist($cm["post_id"],$cm["comment_id"],$thisArr);    
    		$thisArr = $cm; 				    	    		
    	}
    	return $result;
    }


}