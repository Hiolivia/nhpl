<?php

class VideoAction extends CommonAction{
    private $create_fields = ['title','category_id','picture','path','intro'];
    private $edit_fields = ['title','category_id','picture','path','intro'];

    public function index(){
        import('ORG.Util.Page');

        $map['v.closed'] = 0;
        $join = ' LEFT JOIN '.C('DB_PREFIX').'admin a on a.admin_id=v.admin_id LEFT JOIN '.C('DB_PREFIX').'category c on c.category_id=v.category_id';

        $video = D('Video');
        $count = $video->alias('v')->join($join)->where($map)->count();
        $Page = new Page($count, 25);
        $show = $Page->show();

        $list = $video->alias('v')->field('v.*,a.username,c.name')->join($join)->where($map)->order(array('video_id desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
//        $list = $video->alias('v')->where(array('closed'=>0))->order(array('video_id desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as &$row){
            $row['create_time'] = date('Y-m-d H:i:s',$row['create_time']);
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->check($this->create_fields);

            $data['create_time'] = NOW_TIME;
            $data['admin_id'] = $this->getOperator();

            $obj = D('Video');
            if($obj->add($data)){
//                $obj->cleanCache();
                $this->baoSuccess('添加成功',U('video/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $category = D('Category')->field('category_id,name')->where(['type'=>0,'closed'=>0])->select();
            $this->assign('category',$category);
            $this->display();
        }
    }

    public function edit($video_id = 0){
        if ($video_id){
            $obj = D('Video');
            $video = $obj->find($video_id);

            if ($this->isPost()){
                $data = $this->check($this->edit_fields);
                $data['video_id'] = $video_id;
                if($obj->save($data) !== false){
//                    $obj->cleanCache();
                    $this->baoSuccess('操作成功',U('video/index'));
                }
                $this->baoError('操作失败');
            }else{
                $category = D('Category')->field('category_id,name')->where(['type'=>0,'closed'=>0])->select();
                $this->assign('category',$category);

                $this->assign('detail',$video);
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的视频课堂');
        }
    }

    public function delete($video_id = 0){
        if (is_numeric($video_id) && ($video_id = (int) $video_id)) {
            $obj = D('Video');
            $obj->save(array('video_id' => $video_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('video/index'));
        } else {
            /*$video_id = $this->_post('video_id', false);
            if (is_array($video_id)) {
                $obj = D('Video');
                foreach ($video_id as $id) {
                    $obj->save(array('video_id' => $id, 'closed' => 1));
                }
                $this->baoSuccess('删除成功！', U('video/index'));
            }*/
            $this->baoError('请选择要删除的视频');
        }
    }

    private function check($fields){
        $data = $this->checkFields($this->_post('data', false), $fields);

        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('视频标题不能为空');
        }

        $data['category_id'] = (int)$data['category_id'];
        if (empty($data['category_id'])){
            $this->baoError('所属分类不能为空');
        }

        $data['picture'] = htmlspecialchars($data['picture']);
        if (empty($data['picture'])) {
            $this->baoError('请上传封面图片');
        }
        if (!isImage($data['picture'])) {
            $this->baoError('封面图片格式不正确');
        }

        $data['path'] = htmlspecialchars($data['path']);
        if (empty($data['path'])){
            $this->baoError('视频路径不能为空');
        }

        $data['intro'] = htmlspecialchars($data['intro']);
        if(empty($data['intro'])){
            $this->baoError('视频描述不能为空');
        }

        return $data;
    }

    private function getOperator(){
        $admin = session('admin');

        return isset($admin['admin_id']) ? $admin['admin_id'] : 0;
    }
}