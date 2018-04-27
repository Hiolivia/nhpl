<?php
class AppointAction extends CommonAction{
    protected function _initialize(){
        parent::_initialize();
     
    }
    public function index(){
        $this->display();
    }
    public function Appointloading(){
        $Appoint = D('Appoint');
        $Appointorder = D('Appointorder');
        import('ORG.Util.Page');
        $map = array('user_id' => $this->uid);
        $count = $Appointorder->where($map)->count();
        $Page = new Page($count, 5);
        $show = $Page->show();
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Appointorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $appoint_ids = array();
        foreach ($list as $k => $val) {
            $appoint_ids[$val['appoint_id']] = $val['appoint_id'];
        }
        $this->assign('appoints', $Appoint->itemsByIds($appoint_ids));
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
}