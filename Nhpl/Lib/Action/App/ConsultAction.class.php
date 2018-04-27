<?php

class ConsultAction extends CommonAction{

    /*
     * 提问
     * work_id    int     作品id  #必填
     * contents   string  内容    #选填
     * pic        array   图片    #选填
     */
    public function addConsult(){
        $user_id = $this->checkUser();

        $data['contents'] = htmlspecialchars($_POST['contents']);
        $pic = $_POST['pic'];
        if (empty($data['contents']) && empty($pic)){
            $this->errorInfo('内容不能为空');
        }

        if (mb_strlen($data['details']) > 200){
            $this->errorInfo('内容最多200字');
        }

        $data['work_id'] = (int)$_POST['work_id'];
        $work = D('Works')->find($data['work_id']);
        if (empty($work)){
            $this->errorInfo('请选择要提问的作品');
        }

        $data['pic'] = empty($pic) ? '' : R('App/Upload/uploadImg',array($pic));
        $data['pic'] = empty($data['pic']) ? [] : explode(',',$data['pic']);
        $data['pic'] = json_encode($data['pic']);

        $data['user_id'] = $user_id;
        $data['create_time'] = time();

        if (D('Consult')->add($data)){
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
    * 我的提问
    * page
    * pagesize
    */
    public function myConsult(){
        $user_id = 1;//$this->checkUser();
        $this->dealPage($_GET);

        $obj = D('Consult');
        $map['c.user_id'] = $user_id;

        $info = $obj->alias('c')->field('count(consult_id) as num1,COALESCE(sum(reply_num),0) as num2')->where($map)->find();

        if ($info['num1']){
            $join = ' LEFT JOIN '.c('DB_PREFIX').'works w on w.id=c.work_id';
            $result = $obj->alias('c')
                ->field('c.*,w.cover_pic')
                ->where($map)
                ->join($join)
                ->order(['consult_id'=>'desc'])
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();
        }else{
            $result = [];
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $info['num1'],
            'reply_num' => $info['num2'],
            'result' => $this->getUserinfo($result)
        ]);
    }

    /*
    * 顾客咨询
    * work_id   作品id   #必填
    * page
    * pagesize
    */
    public function consultlist(){
        $this->dealPage($_GET);

        $map['work_id'] = (int)$_GET['work_id'];
        $work = D('Works')->find($map['work_id']);
        if (empty($work)){
            $this->stringify([
                'status' => self::BAO_DETAIL_NO_EXSITS,
                'msg' => '请选择要咨询的作品'
            ]);
        }

        $obj = D('Consult');
        $count = $obj->where($map)->count();

        if ($count){
            $result = $obj->where($map)
                ->order(['consult_id'=>'desc'])
                ->limit(($this->page-1)*$this->pagesize,$this->pagesize)
                ->select();
        }else{
            $result = [];
        }

        foreach ($result as &$row){
            $row['cover_pic'] = $work['cover_pic'];
        }

        $this->stringify([
            'status' => self::BAO_REQUEST_SUCCESS,
            'msg' => 'ok',
            'count' => $count,
            'result' => $this->getUserinfo($result)
        ]);
    }

    private function errorInfo($msg){
        $this->stringify([
            'status' => self::BAO_FROM_FALSE,
            'msg' => $msg
        ]);
    }

    //补全用户信息
    private function getUserinfo($result=[]){
        $user_ids = $users = [];
        foreach ($result as $row){
            if ($row['user_id'] && !in_array($row['user_id'],$user_ids)){
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

        foreach ($result as &$row) {
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