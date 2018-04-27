<?php

class PostcateAction extends CommonAction{
    private $create_fields = ['cate_name','picture','orderby'];
    private $edit_fields = ['cate_name','picture','orderby'];

    public function index(){
        import('ORG.Util.Page');
        $cate = D('PostCate');
        $count = $cate->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $list = $cate->order(array('orderby'=>'asc','cate_id'=>'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->check($this->create_fields);
            $obj = D('PostCate');
            if($obj->add($data)){
//                $obj->cleanCache();
                $this->baoSuccess('添加成功',U('postcate/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    public function edit($cate_id = 0){
        if ($cate_id){
            $obj = D('PostCate');
            $cate = $obj->find($cate_id);

            if ($this->isPost()){
                $data = $this->check($this->edit_fields);
                $data['cate_id'] = $cate_id;
                if($obj->save($data) !== false){
//                    $obj->cleanCache();
                    $this->baoSuccess('操作成功',U('postcate/index'));
                }
                $this->baoError('操作失败');
            }else{
                $this->assign('detail',$cate);
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的类别');
        }
    }

    public function change($cate_id = 0){
        if ($cate_id){
            $obj = D('PostCate');
            $cate = $obj->find($cate_id);
            $data = ['cate_id'=>$cate_id,'closed'=>$cate['closed']?0:1];
            if ($obj->save($data)){
                $this->baoSuccess('操作成功',U('postcate/index'));
            }
            $this->baoError('操作失败');
        }else{
            $this->baoError('请选择要操作的分类');
        }
    }

    private function check($fields = []){
        $data = $this->checkFields($this->_post('data', false), $fields);

        $data['cate_name'] = htmlspecialchars($data['cate_name']);
        if (empty($data['cate_name'])) {
            $this->baoError('类别名称不能为空');
        }

        $data['picture'] = htmlspecialchars($data['picture']);
        if (empty($data['picture'])) {
            $this->baoError('请上传封面图片');
        }
        if (!isImage($data['picture'])) {
            $this->baoError('封面图片格式不正确');
        }
        $data['orderby'] = (int) $data['orderby'];
        return $data;
    }
}