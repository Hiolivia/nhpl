<?php

class CategoryAction extends CommonAction{
    private $create_fields = ['type','name','picture'];
    private $edit_fields = ['type','name','picture'];

    public function index(){
        import('ORG.Util.Page');
        $schedule = D('Category');
        $count = $schedule->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $list = $schedule->order(array('category_id desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Category');
            if($obj->add($data)){
//                $obj->cleanCache();
                $this->baoSuccess('添加成功',U('category/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    public function edit($category_id = 0){
        if ($category_id){
            $obj = D('Category');
            $category = $obj->find($category_id);

            if ($this->isPost()){
                $data = $this->editCheck();
                $data['category_id'] = $category_id;
                if($obj->save($data) !== false){
//                    $obj->cleanCache();
                    $this->baoSuccess('操作成功',U('category/index'));
                }
                $this->baoError('操作失败');
            }else{
                $this->assign('detail',$category);
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的课堂类别！');
        }
    }

    public function change($category_id = 0){
        if ($category_id){
            $obj = D('Category');
            $category = $obj->find($category_id);
            $data = ['category_id'=>$category_id,'closed'=>$category['closed']?0:1];
            if ($obj->save($data)){
                $this->baoSuccess('操作成功',U('category/index'));
            }
            $this->baoError('操作失败');
        }else{
            $this->baoError('请选择要操作的课堂分类！');
        }
    }

    private function createCheck(){
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);

        $data['name'] = htmlspecialchars($data['name']);
        if (empty($data['name'])) {
            $this->baoError('类别名称不能为空');
        }

        $data['picture'] = htmlspecialchars($data['picture']);
        if (empty($data['picture'])) {
            $this->baoError('请上传封面图片');
        }
        if (!isImage($data['picture'])) {
            $this->baoError('封面图片格式不正确');
        }
        $data['create_time'] = NOW_TIME;
        return $data;
    }

    public function editCheck(){
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);

        $data['name'] = htmlspecialchars($data['name']);
        if (empty($data['name'])) {
            $this->baoError('类别名称不能为空');
        }

        $data['picture'] = htmlspecialchars($data['picture']);
        if (empty($data['picture'])) {
            $this->baoError('请上传封面图片');
        }
        if (!isImage($data['picture'])) {
            $this->baoError('封面图片格式不正确');
        }

        return $data;
    }
}