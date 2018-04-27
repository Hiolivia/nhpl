<?php
class ClassifyAction extends CommonAction
{
    private $create_fields = array('classify');
    private $edit_fields = array('classify');
    public function index()
    {
        $Classify = D('Classify');
        $list = $Classify->select();
//        var_dump($list);exit;
        $this->assign('list', $list);
        // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show);
//        // 赋值分页输出
        $this->display();
        // 输出模板
    }
    public function select()
    {
        $Tuancate = D('Tuancate');
        import('ORG.Util.Page');
        // 导入分页类
        $map = array('closed' => array('IN', '0,-1'));
        if ($cate_name = $this->_param('cate_name', 'htmlspecialchars')) {
            $map['cate_name'] = array('LIKE', '%' . $cate_name . '%');
            $this->assign('cate_name', $cate_name);
        }
        $count = $Tuancate->where($map)->count();
        // 查询满足要求的总记录数
        $Page = new Page($count, 8);
        // 实例化分页类 传入总记录数和每页显示的记录数
        $pager = $Page->show();
        // 分页显示输出
        $list = $Tuancate->where($map)->order(array('cate_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $pager);
        // 赋值分页输出
        $this->display();
        // 输出模板
    }
    public function create($parent_id = 0)
    {
        if ($this->isPost()) {
            $data = $this->createCheck();

            $obj = D('Classify');
//            $data['parent_id'] = $parent_id;

            if ($obj->add($data)) {
//                $obj->cleanCache();
                $this->baoSuccess('添加成功', U('Classify/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->assign('parent_id', $parent_id);
            $this->display();
        }
    }

    private function createCheck()
    {
        $data['classify_name'] = htmlspecialchars($this->_post('classify_name'));

        if (empty($data['classify_name'])) {
            $this->baoError('分类不能为空');
        }
        return $data;
    }
    public function edit()
    {
        $cate_id = $this->_get('cate_id');

        $obj = D('Classify');
        $detail = $obj->find($cate_id);
        if ($this->isPost()) {

            $data = $this->editCheck();
            $cate_id = $_POST['cate_id'];

            $obj->where('id='.$cate_id)->save($data);
            if (false !== $obj->where('id='.$cate_id)->save($data)) {

                $this->baoSuccess('操作成功', U('classify/index'));
            }
            $this->baoError('操作失败');
        } else {
            $this->assign('detail', $detail);
            $this->display();
            }

    }
    private function editCheck()
    {
        $data = $this->_post('data', false);
        $data['classify_name'] = htmlspecialchars($data['classify_name']);
        if (empty($data['classify_name'])) {
            $this->baoError('分类不能为空');
        }

        return $data;
    }
    public function delete($cate_id = 0)
    {
        if (is_numeric($cate_id) && ($cate_id = (int) $cate_id)) {

            $obj = D('Classify');
            $obj->delete($cate_id);

            $this->baoSuccess('删除成功！', U('classify/index'));
        }
    }
    public function update()
    {
        $orderby = $this->_post('orderby', false);
        $obj = D('Tuancate');
        foreach ($orderby as $key => $val) {
            $data = array('cate_id' => (int) $key, 'orderby' => (int) $val);
            $obj->save($data);
        }
        $obj->cleanCache();
        $this->baoSuccess('更新成功', U('tuancate/index'));
    }

}