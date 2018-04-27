<?php



class LifeserviceAction extends CommonAction {
	
	protected function _initialize() {
        parent::_initialize();
		$lifeservice = (int)$this->_CONFIG['operation']['lifeservice'];
		if ($lifeservice == 0) {
				$this->error('此功能已关闭');
				die;
			}
     	}

	public function index() {
		$this->display();
	}
    public function lifeserviceloading() {
        $Houseworksetting = D('Houseworksetting');//类目表
        $Housework = D('Housework');//报名表
        import('ORG.Util.Page'); // 导入分页类
        $map = array('user_id' => $this->uid);
        $count = $Housework->where($map)->count(); // 查询满足要求的总记录数 
        
		$Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show(); // 分页显示输出
		$var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
		$p = $_GET[$var];
		if ($Page->totalPages < $p) {
			die('0');
		}
		
		
        $list = $Housework->where($map)->order(array('housework_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		
        $houseworksetting_ids = array();
        foreach ($list as $k => $val) {
            $houseworksetting_ids[$val['id']] = $val['id'];
        }
		
        $this->assign('houseworksetting', $Houseworksetting->itemsByIds($houseworksetting_ids));
		
        $this->assign('list', $list); // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show); // 赋值分页输出

        $this->display(); // 输出模板
    }

}
