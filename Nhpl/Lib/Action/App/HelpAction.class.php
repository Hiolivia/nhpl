<?php 


class HelpAction extends CommonAction{

    /**
     *发型师帮助中心列表页
     */
    public function  help(){
        $obj = D('Help');
        $result = $obj->select();
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS,'result' =>$result);
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_HELP_ERROR,'result' =>'暂无数据');
            $this->stringify($data);
        }

    }

    /**
     *发型师帮助中心详情页
     */
    public  function  detail(){
        $id = $this->_get('id');
        $obj = D('Help');
        $result = $obj->where('id='.$id)->find();
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS,'result' =>$result);
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_HELP_ERROR,'result' =>'暂无数据');
            $this->stringify($data);
        }
    }

    /**
     *发型师意见反馈
     */
    public function opinion(){
//        echo clearUid();exit;
        if(!getUid()){
            $data = array('status' => self::BAO_REG_NO_FIND,'result' =>'尚未登录');
            $this->stringify($data);
        }

        $res = array(
            'uid' => getUid(),
            'content' => $this->_get('content'),
            'time' => time(),
            'channel' => 1
        );

        $result = D('Content')->add($res);
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS,'result' =>'发表成功');
            $this->stringify($data);
        }


    }


















}
