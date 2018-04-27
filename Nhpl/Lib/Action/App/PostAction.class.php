<?php

class PostAction extends CommonAction{

    //分类列表
    public function catelist(){
        $cates = D('PostCate')
            ->where(['closed'=>0])
            ->order(['orderby'=>'asc','cate_id'=>'asc'])
            ->select();

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'result' => $cates
        ]);
    }

    //标签列表
    public function labellist(){
        $labels = D('PostLabel')
            ->where(['closed'=>0])
            ->order(['orderby'=>'asc','label_id'=>'asc'])
            ->select();

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'result' => $labels
        ]);
    }

    /*
     * 发帖
     * title      string  标题    #必填
     * details    string  内容    #选填
     * pic        string  图片    #选填
     * cate_id    int     类别id  #必填
     * label_id   array   标签id  #必填
     */
    public function addPost(){

        $user_id = $this->checkUser();

        $data['title'] = htmlspecialchars($_POST['title']);
        if (empty($data['title']) || mb_strlen($data['title']) > 30) {
            $this->errorInfo('标题不符合规范（不为空且最多30字）');
        }

        $data['details'] = htmlspecialchars($_POST['details']);
        $pic = $_POST['pic'];
        if (empty($data['details']) && empty($pic)){
            $this->errorInfo('内容不能为空');
        }

        if (mb_strlen($data['details']) > 500){
            $this->errorInfo('内容最多500字');
        }

        $data['cate_id'] = (int)$_POST['cate_id'];
        if(empty($data['cate_id'])){
            $this->errorInfo('分类不能为空');
        }

        $data['label_id'] = $_POST['label_id'];
        if (empty($data['label_id'])){
            $this->errorInfo('标签不能为空');
        }
        if (!is_array($data['label_id'])){
            $this->errorInfo('标签参数不符合规范');
        }
        $data['label_id'] = implode(',',$data['label_id']);

        $data['pic'] = empty($pic) ? '' : R('App/Upload/uploadImg',array($pic));
        $data['pic'] = empty($data['pic']) ? [] : explode(',',$data['pic']);
        $data['pic'] = json_encode($data['pic']);

        $data['user_id'] = $user_id;
        $data['create_time'] = time();

        if (D('Post')->add($data)){
            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok'
            ]);
        }else{
            $this->stringify([
                'status' => self::BAO_ADD_FALSE,
                'msg' => '操作失败'
            ]);
        }
    }

    /*
     * 帖子详情
     * post_id  帖子id  #必填
    */
    public function detail(){
        $post_id = (int)$_GET['post_id'];

        $post = D('Post')->find($post_id);
        if (empty($post) || $post['closed']){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '该帖子不存在，请确认后再操作'
            ]);
        }else{
            $post['create_time'] = date('Y-m-d H:i:s');
            $post['pic'] = json_decode($post['pic']);

            //user
            $user = D('Users')->field('user_id,account,nickname,face')->find($post['user_id']);
            $post['user_name'] = empty($user) ? '' : $user['nickname'];
            $post['face'] = empty($user) ? '' : $user['face'];

            $post['cate_name'] = D('PostCate')->find($post['cate_id'])['cate_name'];

            //label
            $label_ids = explode(',',$post['label_id']);
            if (empty($label_ids)){
                $post['label_info'] = [];
            }else{
                $labels = D('PostLabel')->field('label_id,label_name')->where(['label_id'=>['IN',$label_ids]])->select();
                foreach ($labels as $label){
                    $post['label_info'][$label['label_id']] = $label['label_name'];
                }
            }

            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok',
                'result' => $post
            ]);
        }
    }

    //最近更新(最新发布的6条)
    public function recentlist(){
        $join = ' LEFT JOIN '.c('DB_PREFIX').'post_cate c on c.cate_id=p.cate_id';
        $result = D('Post')->alias('p')
            ->field('p.*,c.cate_name')
            ->join($join)
            ->where(['p.closed'=>0])
            ->order(['post_id'=>'desc'])
            ->limit(6)
            ->select();

        $result = $this->getUserinfo($result);

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'result' => $result
        ]);
    }

    /*
     * 精选内容
     * page
     * pagesize
    */
    public function finelist(){
        $this->dealPage($_GET);

        $map['p.closed'] = 0;
        $map['p.is_fine'] = 1;
        $obj = D('Post');
        $count = $obj->alias('p')->where($map)->count();

        if ($count){
            $join = ' LEFT JOIN '.c('DB_PREFIX').'post_cate c on c.cate_id=p.cate_id';
            $result = $obj->alias('p')
                ->field('p.*,c.cate_name')
                ->join($join)
                ->where($map)
                ->order(['post_id'=>'desc'])
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();

            $result = $this->getUserinfo($result);
        }else{
            $result = [];
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $count,
            'result' => $result
        ]);
    }

    /*
    * 相关推荐
    * post_id  帖子id  #必填
    */
    public function recommendlist(){
        $post_id = (int)$_GET['post_id'];

        $post = D('Post')->field('post_id,closed,cate_id')->find($post_id);
        if (empty($post) || $post['closed']){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '该帖子不存在，请确认后再操作'
            ]);
        }

        $result = D('Post')
            ->field('post_id,title,details,pic')
            ->where(['closed'=>0,'cate_id'=>$post['cate_id'],'post_id'=>['neq',$post_id]])
            ->order(['post_id'=>'desc'])
            ->limit(3)
            ->select();

        foreach ($result as &$row){
            $row['pic'] = json_decode($row['pic']);
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'result' => $result
        ]);
    }

    /*
    * 分类帖子列表
    * cate_id  分类id  #必填
    * page
    * pagesize
    */
    public function postlist(){
        $cate_id = (int)$_GET['cate_id'];
        $cate = D('PostCate')->find($cate_id);
        if (empty($cate) || $cate['closed']){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '请选择要查看的帖子分类'
            ]);
        }

        $this->dealPage($_GET);

        $map['closed'] = 0;
        $map['cate_id'] = $cate_id;
        $count = D('Post')->where($map)->count();

        if ($count){
            $result = D('Post')
                ->where($map)
                ->order(['post_id'=>'desc'])
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();

            $result = $this->getUserinfo($result);
            foreach ($result as &$row){
                $row['cate_name'] = $cate['cate_name'];
            }
        }else{
            $result = [];
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $count,
            'result' => $result
        ]);

    }

    /*
    * 帖子点赞
    * post_id  帖子id  #必填
    */
    public function praise(){
        $user_id = $this->checkUser();

        $post_id = (int)$_POST['post_id'];

        if ($post_id){
            $this->checkPost($post_id);

            $map['post_id'] = $post_id;
            $map['user_id'] = $user_id;

            $obj = D('PostZan');
            $ret = $obj->where($map)->find();
            if (empty($ret)){
                $map['create_time'] = time();
                if ($obj->add($map)){
                    D('Post')->where(['post_id'=>$post_id])->setInc('zan_num');
                }
            }

            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok'
            ]);
        }else{
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '请选择要点赞的帖子'
            ]);
        }
    }

    /*
    * 帖子浏览
    * post_id  帖子id  #必填
    */
    public function browse(){
        $post_id = (int)$_POST['post_id'];

        if ($post_id){
            $this->checkPost($post_id);

            D('Post')->where(['post_id'=>$post_id])->setInc('views');

            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok'
            ]);
        }else{
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '请选择要浏览的帖子'
            ]);
        }
    }

    /*
     * 发评论
     * post_id    int     帖子id  #选填（回复帖子时必填）
     * reply_id   int     评论id  #选填（回复评论时必填）
     * contents   string  内容    #选填（内容和图片至少填一项）
     * pic        string  图片    #选填
     */
    public function addReply(){
        $user_id = $this->checkUser();

        $post_id = (int)$_POST['post_id'];
        $reply_id = (int)$_POST['reply_id'];
        if(!$post_id && !$reply_id){
            $this->errorInfo('参数错误');
        }

        if($reply_id){
            //回复评论
            $reply = D('PostReply')->find($reply_id);
            if (empty($reply) || $reply['closed']){
                $this->errorInfo('评论不存在，请确认后再操作');
            }

            $data['post_id'] = $post_id = $reply['post_id'];
            $data['p_id'] = $reply['reply_id'];

        }elseif ($post_id){
            //回复帖子
            $this->checkPost($post_id);

            $data['post_id'] = $post_id;
            $data['p_id'] = 0;
        }

        $data['contents'] = htmlspecialchars($_POST['contents']);
        $pic = $_POST['pic'];
        if (empty($data['contents']) && empty($pic)){
            $this->errorInfo('内容不能为空');
        }
        if (mb_strlen($data['contents']) > 200){
            $this->errorInfo('内容最多200字');
        }

        $data['pic'] = empty($pic) ? '' : R('App/Upload/uploadImg',array($pic));
        $data['pic'] = empty($data['pic']) ? [] : explode(',',$data['pic']);
        $data['pic'] = json_encode($data['pic']);

        $data['user_id'] = $user_id;
        $data['create_time'] = time();

        if (D('PostReply')->add($data)){
            D('Post')->where(['post_id'=>$post_id])->setInc('reply_num');
            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok'
            ]);
        }else{
            $this->stringify([
                'status' => self::BAO_ADD_FALSE,
                'msg' => '操作失败'
            ]);
        }
    }

    /*
    * 评论列表
    * post_id  帖子id  #必填
    * page
    * pagesize
    */
    public function replylist(){
        $post_id = (int)$_GET['post_id'];
        $this->checkPost($post_id);
        $this->dealPage($_GET);

        $map['closed'] = 0;
        $map['post_id'] = $post_id;

        $count = D('PostReply')->where($map)->count();
        if ($count){
            $result = D('PostReply')
                ->where($map)
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();

            $result = $this->getUserinfo($result);

            //父级评论补全
            $p_ids = $p_replys = [];
            foreach ($result as $row) {
                if ($row['p_id'] && !in_array($row['p_id'],$p_ids)){
                    $p_ids = $row['p_id'];
                }
            }

            if (!empty($p_ids)){
                $p_result = D('PostReply')->where(['reply_id'=>['IN',$p_ids]])->select();
                $p_result = $this->getUserinfo($p_result);
                foreach ($p_result as $row) {
                    $p_replys[$row['reply_id']] = $row;
                }
            }

            foreach ($result as $key => &$row){
                $row['desc'] = (($this->page-1)*$this->pagesize+$key+1).'楼';
                $row['p_reply'] = isset($p_replys[$row['p_id']]) ? $p_replys[$row['p_id']] : [];
            }
        }else{
            $result = [];
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $count,
            'result' => $result
        ]);
    }

    /*
    * 评论点赞
    * reply_id  评论id  #必填
    */
    public function replyPraise(){

        $user_id = $this->checkUser();

        $reply_id = (int)$_POST['reply_id'];

        if ($reply_id){
            $this->checkReply($reply_id);

            $map['reply_id'] = $reply_id;
            $map['user_id'] = $user_id;

            $obj = D('PostReplyZan');
            $ret = $obj->where($map)->find();
            if (empty($ret)){
                $map['create_time'] = time();
                if ($obj->add($map)){
                    D('PostReply')->where(['reply_id'=>$reply_id])->setInc('zan_num');
                }
            }

            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok'
            ]);
        }else{
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '请选择要点赞的评论'
            ]);
        }
    }

    /*
    * 发型师|作品|帖子收藏
    * type     类型（1发型师 2作品 3帖子） #必填
    * id       发型师|作品|帖子id        #必填
    */
    public function addCollect(){
        $user_id = $this->checkUser();
        $map['user_id'] = $user_id;

        $map['type'] = (int)$_POST['type'];
        if (!in_array($map['type'],[1,2,3])){
            $this->errorInfo('参数错误');
        }

        $map['id'] = (int)$_POST['id'];
        if(!$map['id']){
            $this->errorInfo('请选择要收藏的信息');
        }

        if($map['type'] == 1){
            //发型师收藏
            $info = D('Huser')->find($map['id']);
        }elseif($map['type'] == 2){
            //作品收藏
            $info = D('Works')->find($map['id']);
        }elseif($map['type'] == 3){
            //帖子收藏
            $info = D('Post')->find($map['id']);
        }

        if (empty($info)){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '您收藏的信息不存在，请确认后再操作'
            ]);
        }

        $obj = D('UsersCollect');
        $collect = $obj->where($map)->find();
        if (empty($collect)){
            $map['create_time'] = time();
            $obj->add($map);
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok'
        ]);
    }

    /*
    * 收藏删除
    * collect_id   收藏id   #必填
    */
    public function delCollect(){
        $user_id = $this->checkUser();

        $collect_id = (int)$_POST['collect_id'];

        $obj = D('UsersCollect');
        $collect = $obj->find($collect_id);

        if (empty($collect) || $collect['user_id']!=$user_id){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '该收藏信息异常，请确认后再操作'
            ]);
        }

        $obj->delete($collect_id);

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok'
        ]);
    }

    /*
    * 我的收藏
    * type   类型（1发型师 2作品 3帖子）   #必填
    * page
    * pagesize
    */
    public function collectlist(){
        $user_id = $this->checkUser();

        $type = (int)$_GET['type'];
        if (!in_array($type,[1,2,3])){
            $this->errorInfo('参数错误');
        }

        $this->dealPage($_GET);

        $obj = D('UsersCollect');
        $map['c.type'] = $type;
        $map['c.user_id'] = $user_id;

        if($type == 1){
            //发型师收藏列表
            //待实现
            $count = 0;
            $result = [];

        }elseif($type == 2){
            //作品收藏列表
            $join = ' JOIN '.c('DB_PREFIX').'works w on w.id=c.id';
            $count = $obj->alias('c')->where($map)->join($join)->count();

            if ($count){
                $join .= ' LEFT JOIN '.c('DB_PREFIX').'classify f on f.id=w.c_id';
                $result = $obj->alias('c')
                    ->field('collect_id,w.id,w.uid,w.cover_pic,w.c_id,w.r_nums,w.time,f.classify_name')
                    ->where($map)
                    ->join($join)
                    ->order(['collect_id'=>'desc'])
                    ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                    ->select();
            }else{
                $result = [];
            }

            //补全发型师信息
            $huser_ids = $husers = [];
            foreach ($result as $row){
                if($row['uid'] && !in_array($row['uid'],$huser_ids)){
                    $huser_ids[] = $row['uid'];
                }
            }

            if (!empty($huser_ids)){
                $userinfo = D('Huser')
                    ->field('huser_id,account,name,header_pic')
                    ->where(['huser_id'=>['IN',$huser_ids]])
                    ->select();

                foreach ($userinfo as $user){
                    $husers[$user['huser_id']] = $user;
                }
            }

            foreach ($result as &$row){
                $row['time'] = date('Y-m-d H:i:s',$row['time']);

                if (isset($husers[$row['uid']])){
                    $row['username'] = $husers[$row['uid']]['name'];
                    $row['header_pic'] = $husers[$row['uid']]['header_pic'];
                }else{
                    $row['username'] = $row['header_pic'] = '';
                }
            }

        }elseif($type == 3){
            //帖子收藏列表
            $map['p.closed'] = 0;
            $join = ' LEFT JOIN '.C('DB_PREFIX').'post p on c.id = p.post_id';

            $count = $obj->alias('c')->where($map)->join($join)->count();

            if ($count){
                $join .= ' LEFT JOIN '.C('DB_PREFIX').'post_cate a on a.cate_id=p.cate_id';
                $result = $obj->alias('c')
                    ->field('collect_id,p.*,a.cate_name')
                    ->where($map)
                    ->join($join)
                    ->order(['collect_id'=>'desc'])
                    ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                    ->select();
            }else{
                $result = [];
            }

            $result = $this->getUserinfo($result);
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $count,
            'result' => $result
        ]);
    }

    /*
    * 我的帖子
    * page
    * pagesize
    */
    public function myPost(){
        $user_id = $this->checkUser();
        $this->dealPage($_GET);

        $obj = D('Post');
        $map['p.closed'] = 0;
        $map['p.user_id'] = $user_id;

        $info = $obj->alias('p')->field('count(post_id) as num1,COALESCE(sum(reply_num),0) as num2,COALESCE(sum(zan_num),0) as num3')->where($map)->find();

        if ($info['num1']){
            $join = ' LEFT JOIN '.c('DB_PREFIX').'post_cate c on c.cate_id=p.cate_id';
            $result = $obj->alias('p')
                ->field('p.*,c.cate_name')
                ->where($map)
                ->join($join)
                ->order(['post_id'=>'desc'])
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();
        }else{
            $result = [];
        }

        //补全用户信息
        $result = $this->getUserinfo($result);

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $info['num1'],
            'reply_num' => $info['num2'],
            'zan_num' => $info['num3'],
            'result' => $result
        ]);
    }

    private function checkPost($post_id = 0){
        $info = D('Post')->find($post_id);

        if (empty($info) || $info['closed']){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '该帖子不存在，请确认后再操作'
            ]);
        }
    }

    private function checkReply($reply_id = 0){
        $info = D('PostReply')->find($reply_id);

        if (empty($info) || $info['closed']){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '该评论不存在，请确认后再操作'
            ]);
        }
    }

    private function errorInfo($msg){
        $this->stringify([
            'status' => self::BAO_FROM_FALSE,
            'msg' => $msg
        ]);
    }

    private function getUserinfo($result = []){
        $user_ids = $users = [];
        foreach ($result as $row){
            if($row['user_id'] && !in_array($row['user_id'],$user_ids)){
                $user_ids[] = $row['user_id'];
            }
        }

        if (!empty($user_ids)){
            $userinfo = D('Users')
                ->field('user_id,account,face,nickname')
                ->where(['user_id'=>['IN',$user_ids]])
                ->select();

            foreach ($userinfo as $user){
                $users[$user['user_id']] = $user;
            }
        }

        foreach ($result as &$row){
            $row['pic'] = json_decode($row['pic']);
            $row['create_time'] = date('Y-m-d H:i:s',$row['create_time']);

            if (isset($users[$row['user_id']])){
                $row['user_name'] = $users[$row['user_id']]['nickname'];
                $row['face'] = $users[$row['user_id']]['face'];
            }else{
                $row['user_name'] = $row['face'] = '';
            }
        }

        return $result;
    }
}