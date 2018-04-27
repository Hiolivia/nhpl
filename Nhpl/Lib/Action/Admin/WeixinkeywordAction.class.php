<?php



class WeixinkeywordAction extends CommonAction {

    private $create_fields = array('keyword', 'type', 'title', 'contents', 'url', 'photo');
    private $edit_fields = array('keyword', 'type', 'title', 'contents', 'url', 'photo');

    public function index() {
        $Weixinkeyword = D('Weixinkeyword');
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['keyword'] = array('LIKE', '%' . $keyword . '%');
        }
        $count = $Weixinkeyword->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 15); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Weixinkeyword->where($map)->order(array('keyword_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Weixinkeyword');
            if ($obj->add($data)) {
                $obj->cleanCache();
                $this->baoSuccess('添加成功', U('weixinkeyword/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['keyword'] = htmlspecialchars($data['keyword']);
        if (empty($data['keyword'])) {
            $this->baoError('关键字不能为空');
        }
        if (D('Weixinkeyword')->checkKeyword($data['keyword'])) {
            $this->baoError('关键字已经存在');
        }

        if (empty($data['type'])) {
            $this->baoError('类型不能为空');
        }
        $data['title'] = htmlspecialchars($data['title']);


        if (empty($data['contents'])) {
            $this->baoError('回复内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['contents'])) {
            $this->baoError('内容含有敏感词：' . $words);
        }
        $data['url'] = htmlspecialchars($data['url']);
        $data['photo'] = htmlspecialchars($data['photo']);
        if (!empty($data['photo']) && !isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        }
        return $data;
    }

    public function edit($keyword_id = 0) {
        if ($keyword_id = (int) $keyword_id) {
            $obj = D('Weixinkeyword');
            if (!$detail = $obj->find($keyword_id)) {
                $this->baoError('请选择要编辑的微信关键字');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['keyword_id'] = $keyword_id;
                $local = $obj->checkKeyword($data['keyword']);
                if ($local && $local['keyword_id'] != $keyword_id) {
                    $this->baoError('关键字已经存在');
                }

                if (false !== $obj->save($data)) {
                    $obj->cleanCache();
                    $this->baoSuccess('操作成功', U('weixinkeyword/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的微信关键字');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['keyword'] = htmlspecialchars($data['keyword']);
        if (empty($data['keyword'])) {
            $this->baoError('关键字不能为空');
        }
        if (empty($data['type'])) {
            $this->baoError('类型不能为空');
        }
        $data['title'] = htmlspecialchars($data['title']);

        if (empty($data['contents'])) {
            $this->baoError('回复内容不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['contents'])) {
            $this->baoError('内容含有敏感词：' . $words);
        }
        $data['url'] = htmlspecialchars($data['url']);
        $data['photo'] = htmlspecialchars($data['photo']);
        if (!empty($data['photo']) && !isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        }
        return $data;
    }

    public function delete($keyword_id = 0) {
        if (is_numeric($keyword_id) && ($keyword_id = (int) $keyword_id)) {
            $obj = D('Weixinkeyword');
            $obj->delete($keyword_id);
            $obj->cleanCache();
            $this->baoSuccess('删除成功！', U('weixinkeyword/index'));
        } else {
            $keyword_id = $this->_post('keyword_id', false);
            if (is_array($keyword_id)) {
                $obj = D('Weixinkeyword');
                foreach ($keyword_id as $id) {
                    $obj->delete($id);
                }
                $obj->cleanCache();
                $this->baoSuccess('删除成功！', U('weixinkeyword/index'));
            }
            $this->baoError('请选择要删除的微信关键字');
        }
    }

    public function mass() {
        if ($this->isPost()) {
            $data = $this->checkFields($this->_post('data', false), array('title', 'contents', 'url', 'photo'));
            $data['title'] = htmlspecialchars($data['title']);
            $data['contents'] = SecurityEditorHtml($data['contents']);
            $data['url'] = htmlspecialchars($data['url']);
            $data['photo'] = htmlspecialchars($data['photo']);
            if (!isImage($data['photo'])) {
                $this->baoError('请上传缩略图');
            }
            $result =  D('Weixin')->mass($data);
           if(true !==$result){
               $this->baoError($result);
           }    
           $this->baoSuccess('发送成功！',U('weixinkeyword/mass'));
        } else {
            $this->display();
        }
    }

}
