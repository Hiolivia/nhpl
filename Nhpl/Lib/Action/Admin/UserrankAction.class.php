<?php


class UserrankAction extends CommonAction{
    private $create_fields = array('rank_name','icon','icon1','prestige','rebate');
    private $edit_fields = array('rank_name','icon','icon1','prestige','rebate');
    
    public  function index(){
       $Userrank = D('Userrank');
       import('ORG.Util.Page');// 导入分页类
       $map = array();
       $count      = $Userrank->where($map)->count();// 查询满足要求的总记录数 
       $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
       $show       = $Page->show();// 分页显示输出
       $list = $Userrank->where($map)->order(array('rank_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();
       $this->assign('list',$list);// 赋值数据集www.hatudou.com  二开开发qq  120585022
       $this->assign('page',$show);// 赋值分页输出
       $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Userrank');
        if($obj->add($data)){
            $obj->cleanCache();
            $this->baoSuccess('添加成功',U('userrank/index'));
        }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['rank_name'] = htmlspecialchars($data['rank_name']);
        if(empty($data['rank_name'])){
            $this->baoError('等级名称不能为空');
        }  
        $data['icon'] = htmlspecialchars($data['icon']);
//        if(empty($data['icon'])){
//            $this->baoError('等级图标不能为空');
//        }
        $data['icon1'] = htmlspecialchars($data['icon1']);
        $data['prestige'] = (int)$data['prestige'];
        $data['rebate'] = (int)$data['rebate'];
        return $data;
    }
    public function edit($rank_id = 0){
        if($rank_id =(int) $rank_id){
            $obj = D('Userrank');
            if(!$detail = $obj->find($rank_id)){
                $this->baoError('请选择要编辑的会员等级');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['rank_id'] = $rank_id;
                if(false!==$obj->save($data)){
                    $obj->cleanCache();
                    $this->baoSuccess('操作成功',U('userrank/index'));
                }
                $this->baoError('操作失败');
                
            }else{
                $this->assign('detail',$detail);         
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的会员等级');
        }
    }
     private function editCheck(){
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['rank_name'] = htmlspecialchars($data['rank_name']);
        if(empty($data['rank_name'])){
            $this->baoError('等级名称不能为空');
        }       
        $data['icon'] = htmlspecialchars($data['icon']);
//        if(empty($data['icon'])){
//            $this->baoError('等级图标不能为空');
//        }
        $data['icon1'] = htmlspecialchars($data['icon1']);
        $data['rebate'] = (int)$data['rebate'];
        $data['prestige'] = (int)$data['prestige'];
        return $data;  
    }

    public function delete($rank_id = 0){
         if(is_numeric($rank_id) && ($rank_id = (int)$rank_id)){
             $obj =D('Userrank');
             $obj->delete($rank_id);
             $obj->cleanCache();
             $this->baoSuccess('删除成功！',U('userrank/index'));
         }else{
            $rank_id = $this->_post('rank_id',false);
            if(is_array($rank_id)){     
                $obj = D('Userrank');
                foreach($rank_id as $id){
                    $obj->delete($id);
                }                
                $obj->cleanCache();
                $this->baoSuccess('删除成功！', U('userrank/index'));
            }
            $this->baoError('请选择要删除的会员等级');
         }
         
    }

    public  function hlabel(){
        $Userlabel = D('user_label');
        import('ORG.Util.Page');// 导入分页类
        $map = array();
        $count      = $Userlabel->where($map)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $shows       = $Page->show();// 分页显示输出
        $lists = $Userlabel->where($map)->order(array('rank_id'=>'desc'))->limit($Page->firstRow.','.$Page->listRows)->select();

//        var_dump($lists);exit;
        $this->assign('list',$lists);// 赋值数据集www.hatudou.com  二开开发qq  120585022

        $this->assign('page',$shows);// 赋值分页输出
        $this->display(); // 输出模板
    }

    public function creates() {
        if ($this->isPost()) {
            $data = $this->createChecks();

            $obj = D('user_label');
            if($obj->add($data)){
//                $obj->cleanCache();
                $this->baoSuccess('添加成功',U('userrank/hlabel'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createChecks() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['rank_name'] = htmlspecialchars($data['rank_name']);
        if(empty($data['rank_name'])){
            $this->baoError('等级名称不能为空');
        }
        $data['icon'] = htmlspecialchars($data['icon']);
//        if(empty($data['icon'])){
//            $this->baoError('等级图标不能为空');
//        }
        $data['icon1'] = htmlspecialchars($data['icon1']);
        $data['prestige'] = (int)$data['prestige'];
        $data['rebate'] = (int)$data['rebate'];
        return $data;
    }
    public function edits($rank_id = 0){
        if($rank_id =(int) $rank_id){
            $obj = D('user_label');
            if(!$detail = $obj->find($rank_id)){
                $this->baoError('请选择要编辑的会员等级');
            }
            if ($this->isPost()) {
                $data = $this->editChecks();
                $data['rank_id'] = $rank_id;
                if(false!==$obj->save($data)){
//                    $obj->cleanCache();
                    $this->baoSuccess('操作成功',U('userrank/hlabel'));
                }
                $this->baoError('操作失败');

            }else{
                $this->assign('detail',$detail);
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的会员等级');
        }
    }
    private function editChecks(){
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['rank_name'] = htmlspecialchars($data['rank_name']);
        if(empty($data['rank_name'])){
            $this->baoError('等级名称不能为空');
        }
        $data['icon'] = htmlspecialchars($data['icon']);
//        if(empty($data['icon'])){
//            $this->baoError('等级图标不能为空');
//        }
        $data['icon1'] = htmlspecialchars($data['icon1']);
        $data['rebate'] = (int)$data['rebate'];
        $data['prestige'] = (int)$data['prestige'];
        return $data;
    }

    public function deletes($rank_id = 0){
        if(is_numeric($rank_id) && ($rank_id = (int)$rank_id)){
            $obj =D('user_label');
            $obj->delete($rank_id);
//            $obj->cleanCache();
            $this->baoSuccess('删除成功！',U('userrank/hlabel'));
        }else{
            $rank_id = $this->_post('rank_id',false);
            if(is_array($rank_id)){
                $obj = D('user_label');
                foreach($rank_id as $id){
                    $obj->delete($id);
                }
//                $obj->cleanCache();
                $this->baoSuccess('删除成功！', U('userrank/hlabel'));
            }
            $this->baoError('请选择要删除的会员等级');
        }

    }
   
}
