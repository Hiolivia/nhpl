<?php



class FenzhanshopcashAction extends CommonAction {
	


    public function index() {
		
	
	   
        $Userscash = D('Userscash');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('type' => shop,'city_id'=>$this->city_id);
        if ($account = $this->_param('account', 'htmlspecialchars')) {
            $map['account'] = array('LIKE', '%' . $account . '%');
            $this->assign('account', $account);
        }
        $count = $Userscash->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Userscash->where($map)->order(array('cash_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $ids = array();
        foreach ($list as $row) {
            $ids[] = $row['user_id'];
        }
        $Usersex = D('Usersex');
        $map = array();
        $map['user_id'] = array('in', $ids);
        $ex = $Usersex->where($map)->select();
        $tmp = array();
        foreach ($ex as $row) {
            $tmp[$row['user_id']] = $row;
        }
        foreach ($list as $key => $row) {
            $list[$key]['bank_name'] =  empty($list[$key]['bank_name']) ? $tmp[$row['user_id']]['bank_name'] :$list[$key]['bank_name'];
            $list[$key]['bank_num'] =  empty($list[$key]['bank_num']) ? $tmp[$row['user_id']]['bank_num'] :$list[$key]['bank_num'];
            $list[$key]['bank_branch'] =  empty($list[$key]['bank_branch']) ? $tmp[$row['user_id']]['bank_branch'] :$list[$key]['bank_branch'];
            $list[$key]['bank_realname'] =  empty($list[$key]['bank_realname']) ? $tmp[$row['user_id']]['bank_realname'] :$list[$key]['bank_realname'];
        }
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
	



   
}
