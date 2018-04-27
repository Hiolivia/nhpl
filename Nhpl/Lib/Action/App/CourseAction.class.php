<?php

class CourseAction extends CommonAction{

    //课堂分类
    public function category($type = 0 ){
        $map['closed'] = 0;
        $map['type'] = $type;

        $obj = D('Category');
        $category = $obj->where($map)->select();

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'result' => $category
        ]);
    }

    /*
     * 视频课堂列表
     * category_id 分类id #选填
     * page
     * pagesize
     */
    public function videolist(){
        $param = $_GET;
        $this->dealPage($param);

        $map['v.closed'] = 0;
        if (isset($param['category_id'])){
            $map['v.category_id'] = $param['category_id'];
        }

        $obj = D('Video');

        //count
        $count = $obj->alias('v')->where($map)->count();

        if ($count){
            $join = ' LEFT JOIN '.C('DB_PREFIX').'admin a on a.admin_id=v.admin_id LEFT JOIN '.C('DB_PREFIX').'category c on c.category_id=v.category_id';

            $video = $obj->alias('v')
                ->where($map)
                ->field('v.*,a.username,c.name as category_name')
                ->join($join)->order('video_id desc')
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();
        }else{
            $video = [];
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $count,
            'result' => $video
        ]);
    }

    /*
     * 语音课堂列表
     * category_id 分类id #选填
     * page
     * pagesize
     */
    public function voicelist(){
        $param = $_GET;
        $this->dealPage($param);

        $map['v.closed'] = 0;
        if (isset($param['category_id'])){
            $map['v.category_id'] = $param['category_id'];
        }

        $obj = D('Voice');

        //count
        $count = $obj->alias('v')->where($map)->count();
        if ($count){
            $join = ' LEFT JOIN '.C('DB_PREFIX').'category c on c.category_id=v.category_id';

            $voice = $obj->alias('v')
                ->where($map)
                ->field('v.*,c.name as category_name')
                ->join($join)->order('voice_id desc')
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();
        }else{
            $voice = [];
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $count,
            'result' => $voice
        ]);
    }

    //点赞
    public function praise(){
        $video_id = $_POST['video_id'];

        if ($video_id){
            $huser_id = getUid();
            if ($huser_id){
                $this->checkVideo($video_id);

                $map['video_id'] = $video_id;
                $map['uid'] = $huser_id;

                $obj = D('VideoPraise');
                $ret = $obj->where($map)->find();
                if (empty($ret)){
                    $map['create_time'] = time();
                    if ($obj->add($map)){
                        D('Video')->where(['video_id'=>$video_id])->setInc('praises');
                    }
                }
                $status = self::BAO_REQUEST_SUCCESS;
                $msg = 'ok';
            }else{
                $status = self::BAO_REG_NO_FIND;
                $msg = '请先登录再操作';
            }
        }else{
            $status = self::BAO_DETAIL_NO_EXSITS;
            $msg = '请选择要点赞的视频课堂';
        }

        $this->stringify([
            'status' => isset($status)?$status:0,
            'msg' => isset($msg)?$msg:''
        ]);
    }

    //播放
    public function play(){
        $video_id = $_POST['video_id'];

        if ($video_id){
            $this->checkVideo($video_id);

            D('Video')->where(['video_id'=>$video_id])->setInc('plays');

            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok'
            ]);
        }else{
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '请选择要播放的视频课堂'
            ]);
        }
    }

    /*
     * 视频&语音收藏
     * course_id   视频|语音课堂id   #必填
     * type        0视频 1语音      #必填
     */
    public function collect(){
        $param = $_POST;

        if($param['course_id']){
            if ($param['uid'] = getUid()){
                $course_obj = $param['type'] ? D('Voice'):D('Video');
                $course = $course_obj->find($param['course_id']);

                if (empty($course) || $course['closed']){
                    $status = self::BAO_DETAIL_NO_EXSITS;
                    $msg = '该课堂信息不存在，请确认后再操作';
                }else{
                    $obj = D('Collect');
                    $collect = $obj->where($param)->find();

                    if (empty($collect)){
                        $param['create_time'] = time();
                        $obj->add($param);
                    }

                    $status = self::BAO_REQUEST_SUCCESS;
                    $msg = 'ok';
                }
            }else{
                $status = self::BAO_REG_NO_FIND;
                $msg = '请先登录再操作';
            }
        }else{
            $status = self::BAO_DETAIL_NO_EXSITS;
            $msg = '请选择要收藏的课堂';
        }

        $this->stringify([
            'status' => $status,
            'msg' => $msg
        ]);
    }

    /*
     * 收藏信息删除
     * collect_id   #收藏id
     */
    public function delcollect(){
        if ($huser_id = getUid()){
            $collect_id = $_POST['collect_id'];
            $obj = D('Collect');
            $collect = $obj->find($collect_id);

            if (empty($collect) || $collect['uid']!=$huser_id){
                $status = self::BAO_DETAIL_NO_EXSITS;
                $msg = '该收藏信息异常，请确认后再操作';
            }else{
                $obj->delete($collect_id);

                $status = self::BAO_REQUEST_SUCCESS;
                $msg = 'ok';
            }
        }else {
            $status = self::BAO_REG_NO_FIND;
            $msg = '请先登录再操作';
        }

        $this->stringify([
            'status' => $status,
            'msg' => $msg
        ]);
    }

    /*
     * 我的收藏
     * type   0视频 1语音   #必填
     * page
     * pagesize
     */
    public function collectlist(){
        if ($huser_id = getUid()){
            $this->dealPage($_GET);

            $map['c.type'] = $_GET['type'];
            $map['c.uid'] = $huser_id;
            $map['v.closed'] = 0;

            if ($_GET['type']){
                $join = ' LEFT JOIN '.C('DB_PREFIX').'voice v on c.course_id = v.voice_id LEFT JOIN '.c('DB_PREFIX').'admin a on v.admin_id=a.admin_id';
            }else{
                $join = ' LEFT JOIN '.C('DB_PREFIX').'video v on c.course_id = v.video_id LEFT JOIN '.c('DB_PREFIX').'admin a on v.admin_id=a.admin_id';
            }

            $obj = D('Collect');
            $count = $obj->alias('c')->where($map)->join($join)->count();

            $collect = $obj->alias('c')
                ->field('collect_id,v.*,a.username')
                ->where($map)
                ->join($join)
                ->order(['collect_id desc'])
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();

            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok',
                'count' => $count,
                'result' => $collect
            ]);
        }else{
            $this->stringify([
                'status' => self::BAO_REG_NO_FIND,
                'msg' => '请先登录再操作'
            ]);
        }
    }

    private function checkVideo($video_id = 0){
        $video = D('Video')->find($video_id);
        if (empty($video) || $video['closed']){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '该视频课堂不存在，请确认后再操作'
            ]);
        }
    }
}