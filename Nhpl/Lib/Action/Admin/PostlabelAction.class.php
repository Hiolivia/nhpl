<?php

class PostlabelAction extends CommonAction{
    private $create_fields = ['label_name','orderby'];
    private $edit_fields = ['label_name','orderby'];

    public function index(){
        import('ORG.Util.Page');
        $label = D('PostLabel');
        $count = $label->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $list = $label->order(array('orderby'=>'asc','label_id'=>'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->check($this->create_fields);
            $obj = D('PostLabel');
            if($obj->add($data)){
//                $obj->cleanCache();
                $this->baoSuccess('添加成功',U('postlabel/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    public function edit($label_id = 0){
        if ($label_id){
            $obj = D('PostLabel');
            $label = $obj->find($label_id);

            if ($this->isPost()){
                $data = $this->check($this->edit_fields);
                $data['label_id'] = $label_id;
                if($obj->save($data) !== false){
//                    $obj->cleanCache();
                    $this->baoSuccess('操作成功',U('postlabel/index'));
                }
                $this->baoError('操作失败');
            }else{
                $this->assign('detail',$label);
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的标签');
        }
    }

    public function change($label_id = 0){
        if ($label_id){
            $obj = D('PostLabel');
            $label = $obj->find($label_id);
            $data = ['label_id'=>$label_id,'closed'=>$label['closed']?0:1];
            if ($obj->save($data)){
                $this->baoSuccess('操作成功',U('postlabel/index'));
            }
            $this->baoError('操作失败');
        }else{
            $this->baoError('请选择要操作的标签');
        }
    }

    private function check($fields = []){
        $data = $this->checkFields($this->_post('data', false), $fields);

        $data['label_name'] = htmlspecialchars($data['label_name']);
        if (empty($data['label_name'])) {
            $this->baoError('标签不能为空');
        }

        $data['orderby'] = (int) $data['orderby'];
        return $data;
    }
}