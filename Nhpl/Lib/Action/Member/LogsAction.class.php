<?php



class LogsAction extends CommonAction {

    public function goldlogs() {   //金块日志
        $Usergoldlogs = D('Usergoldlogs');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Usergoldlogs->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Usergoldlogs->where($map)->order(array('log_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
    
    public function moneylogs() {   //余额日志
        $Usermoneylogs = D('Usermoneylogs');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Usermoneylogs->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Usermoneylogs->where($map)->order(array('log_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }
    
    public function cashlogs() {  //提现日志
        $Userscash = D('Userscash');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id'=>$this->uid,'type'=>user);
        $count = $Userscash->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Userscash->where($map)->order(array('cash_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display();
    }
    
    
    public function integral() { //积分日志
        $Userintegrallogs = D('Userintegrallogs');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Userintegrallogs->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Userintegrallogs->where($map)->order(array('log_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
	
	
	public function rechargecard() { //代金券使用列表
        $Rechargecard = D('Rechargecard');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid, 'is_used' => 1);
        $count = $Rechargecard->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 16); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Rechargecard->where($map)->order(array('card_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
	
	
    
}