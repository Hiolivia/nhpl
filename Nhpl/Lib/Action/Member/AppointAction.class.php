<?php
class AppointAction extends CommonAction{
    public function index(){
        $Appoint = D('Appoint');
        $Appointorder = D('Appointorder');
        import('ORG.Util.Page');
        $map = array('user_id' => $this->uid);
        $count = $Appointorder->where($map)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $list = $Appointorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $appoint_ids = array();
        foreach ($list as $k => $val) {
            $appoint_ids[$val['appoint_id']] = $val['appoint_id'];
        }
        $this->assign('appoint', $Appoint->itemsByIds($appoint_ids));
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
}