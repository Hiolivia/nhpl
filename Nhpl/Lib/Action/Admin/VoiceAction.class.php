<?php

class VoiceAction extends CommonAction{
    private $create_fields = ['title','category_id','picture','path','intro'];
    private $edit_fields = ['title','category_id','picture','path','intro'];

    public function index(){
        import('ORG.Util.Page');

        $map['v.closed'] = 0;
        $join = ' LEFT JOIN '.C('DB_PREFIX').'admin a on a.admin_id=v.admin_id LEFT JOIN '.C('DB_PREFIX').'category c on c.category_id=v.category_id';

        $voice = D('Voice');
        $count = $voice->alias('v')->join($join)->where($map)->count();
        $Page = new Page($count, 25);
        $show = $Page->show();

        $list = $voice->alias('v')->field('v.*,a.username,c.name')->join($join)->where($map)->order(array('voice_id desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
//        $list = $video->alias('v')->where(array('closed'=>0))->order(array('voice_id desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

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

            $obj = D('Voice');
            if($obj->add($data)){
//                $obj->cleanCache();
                $this->baoSuccess('添加成功',U('voice/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $category = D('Category')->field('category_id,name')->where(['type'=>1,'closed'=>0])->select();
            $this->assign('category',$category);
            $this->display();
        }
    }

    public function edit($voice_id = 0){
        if ($voice_id){
            $obj = D('Voice');
            $voice = $obj->find($voice_id);

            if ($this->isPost()){
                $data = $this->check($this->edit_fields);
                $data['voice_id'] = $voice_id;
                if($obj->save($data) !== false){
//                    $obj->cleanCache();
                    $this->baoSuccess('操作成功',U('voice/index'));
                }
                $this->baoError('操作失败');
            }else{
                $category = D('Category')->field('category_id,name')->where(['type'=>1,'closed'=>0])->select();
                $this->assign('category',$category);

                $this->assign('detail',$voice);
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的语音课堂');
        }
    }

    public function delete($voice_id = 0){
        if (is_numeric($voice_id) && ($voice_id = (int) $voice_id)) {
            $obj = D('Voice');
            $obj->save(array('voice_id' => $voice_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('voice/index'));
        } else {
            /*$voice_id = $this->_post('voice_id', false);
            if (is_array($voice_id)) {
                $obj = D('Voice');
                foreach ($voice_id as $id) {
                    $obj->save(array('$voice_id' => $id, 'closed' => 1));
                }
                $this->baoSuccess('删除成功！', U('voice/index'));
            }*/
            $this->baoError('请选择要删除的语音');
        }
    }

    private function check($fields){
        $data = $this->checkFields($this->_post('data', false), $fields);

        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('标题不能为空');
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
            $this->baoError('路径不能为空');
        }

        $data['intro'] = htmlspecialchars($data['intro']);
        if(empty($data['intro'])){
            $this->baoError('描述不能为空');
        }

        return $data;
    }

    private function getOperator(){
        $admin = session('admin');

        return isset($admin['admin_id']) ? $admin['admin_id'] : 0;
    }
}