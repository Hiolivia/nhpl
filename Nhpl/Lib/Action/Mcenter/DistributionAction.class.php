<?php
class DistributionAction extends CommonAction{
    public function _initialize(){
        parent::_initialize();
        $distributions = (int) $this->_CONFIG['profit']['profit'];
        //赋值分销开关
        if ($distributions == 0) {
            $this->error('暂无此功能');
            die;
        }
        $profit_min_rank_id = (int) $this->_CONFIG['profit']['profit_min_rank_id'];
        $fuser = $this->member;
        if ($fuser) {
            $flag = false;
            if ($profit_min_rank_id) {
                $modelRank = D('Userrank');
                $rank = $modelRank->find($profit_min_rank_id);
                $userRank = $modelRank->find($fuser['rank_id']);
                if ($rank) {
                    if ($userRank && $userRank['prestige'] >= $rank['prestige']) {
                        $flag = true;
                    } else {
                        $flag = false;
                    }
                } else {
                    $flag = false;
                }
            } else {
                $flag = true;
            }
            if (!$flag) {
                $this->error('对不起您必须达到' . $rank['rank_name'] . '及以上等级才有分销权限');
            }
        }
    }
    public function index(){
        if (empty($this->uid)) {
            header("Location: " . U('mobile/passport/login'));
            die;
        }
		$this->assign('profit_ok', $profit_ok = D('Userprofitlogs')->where(array('user_id' => $this->uid,'is_separate' =>1))->sum('money'));
		$this->assign('profit_cancel',$profit_cancel = D('Userprofitlogs')->where(array('user_id' => $this->uid,'is_separate' =>2))->sum('money'));
        $this->display();
    }
    public function profit(){
		$status = (int) $this->_param('status');
		$this->assign('status', $status);
		$this->assign('nextpage', LinkTo('distribution/profitloaddata',array('status'=>$status,'t' => NOW_TIME, 'p' => '0000')));
        $this->mobile_title = '优惠买单';
		$this->display(); // 输出模板		
    }
	public function profitloaddata(){
        $status = (int) $this->_param('status');
        if (!in_array($status, array(0, 1, 2, 3))) {
            $status = 1;
        }
        $model = D('Userprofitlogs');
        import('ORG.Util.Page');
        $map = array('user_id' => $this->uid, 'is_separate' => $status);
        $count = $model->where($map)->count();
        $Page = new Page($count, 8);
        $show = $Page->show();
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
        $orderby = array('log_id' => 'DESC');
        $list = $model->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('status', $status);
		$this->display();
		
	}
	
    public function subordinate(){
		$level = (int) $this->_param('level');
		$this->assign('level', $level);
		$this->assign('nextpage', LinkTo('distribution/subordinateloaddata',array('level'=>$level,'t' => NOW_TIME, 'p' => '0000')));
        $this->mobile_title = '优惠买单';
		$this->display(); // 输出模板		
    }
	
	public function subordinateloaddata(){
		$level = (int) $this->_param('level');
        if (!in_array($level, array(1, 2, 3))) {
            $level = 1;
        }
        $user = D('Users');
        import('ORG.Util.Page');
        $map = array('closed' => 0, 'fuid' . $level => $this->uid);
        $count = $user->where($map)->count();
        $Page = new Page($count, 8);
        $show = $Page->show();
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
        $orderby = array('user_id' => 'DESC');
        $list = $user->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('level', $level);
        $this->display();
		 
	}
    public function qrcode(){
        $token = 'fuid_' . $this->uid;
        $url = U('mobile/passport/register', array('fuid' => $this->uid));
        $file = baoQrCode($token, $url);
        $this->assign('file', $file);
        $this->display();
    }
    public function poster()
    {
        $token = 'fuid_' . $this->uid;
        $url = U('mobile/passport/register', array('fuid' => $this->uid));
        $file = baoQrCode($token, $url);
        $this->assign('file', $file);
        $this->display();
    }
    public function superior()
    {
        $user = D('Users');
        if ($this->member['fuid1']) {
            $fuser = $user->find($this->member['fuid1']);
        }
        $this->assign('fuser', $fuser);
        $this->display();
    }
}