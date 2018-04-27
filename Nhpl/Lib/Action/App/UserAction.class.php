<?php 


class UserAction extends CommonAction{


    /**
     * 作品上传分类
     *获取分类
     * GET
     */
    public function classify(){
        $Classify = D('Classify');
        $result = $Classify->select();
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS,'result' =>$result);
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_HELP_ERROR,'result' =>'暂无数据');
            $this->stringify($data);
        }
    
    }

    /**
     * 作品上传标签
     *获取标签
     * GET
     */
    public function label(){
        $Label = D('Label');
        $result = $Label->select();
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS,'result' =>$result);
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_HELP_ERROR,'result' =>'暂无数据');
            $this->stringify($data);
        }

    }

    /**
     *发型师上传作品
     * POST
     */
    public function releaseWork(){

        $works = D('Works');
        if(!getUid()){
            $data = array('status' => self::BAO_REG_NO_FIND,'result' =>'尚未登录');
            $this->stringify($data);
        }
        $headimg = $_POST['pic'];
        //字符串
        $pic = R('App/Upload/uploadImgs',array('headimg'=>$headimg));

        //字符串转数组
        $pic = explode(',',$pic);
        $cover_pic = $pic[0];
        //数组转json格式字符串
        $pic = json_encode($pic);

        $info = array(
            'uid' => getUid(),
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'pic' => $pic,
            'cover_pic' => $cover_pic,//封面图片
            'c_id' => $_POST['c_id'],
            'l_id' => $_POST['l_id'],
            'r_nums' => 0,
            'c_nums' => 0,
            'time' => time()
        );

        $result = $works->add($info);

        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS,'msg' =>true);
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_HELP_ERROR,'msg' =>false);
            $this->stringify($data);
        }
    }

    /**
     *发型师我的作品列表
     * GET
     */
    public function myworks(){
        if(!getUid()){
            $data = array('status' => self::BAO_REG_NO_FIND,'result' =>'尚未登录');
            $this->stringify($data);
        }
        $uid = getUid();
        $works = D('Works');
        $result = $works->where('uid='.$uid)->field('id,cover_pic,r_nums,time')->select();
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS,'result' =>$result);
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_HELP_ERROR,'result' =>'暂无数据');
            $this->stringify($data);
        }

    }

    /**
     *发型师作品详情页
     * GET
     */
    public function worksDetail(){
        $works = D('Works');
        $id = $this->_get('id');
        $result = $works->where('id='.$id)->find();
        $lid = explode(',',$result['l_id']);
        foreach ($lid as $k=>$v){
            $result['label'][$k] = D('Label')->where('id='.$v)->getField('label_name');
        }
        $result['label'] = json_encode($result['label']);

        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS,'result' =>$result);
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_HELP_ERROR,'result' =>'暂无数据');
            $this->stringify($data);
        }

    }









}
