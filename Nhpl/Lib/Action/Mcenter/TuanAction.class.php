<?php
class TuanAction extends CommonAction
{
    public function index()
    {
        $aready = (int) $this->_param('aready');
        $this->assign('aready', $aready);
        $this->display();
        // 输出模板
    }
    public function delete($order_id)
    {
        $obj = D('Tuanorder');
        if (!($detail = D('Tuanorder')->find($order_id))) {
            $this->error('该团购不存在或者已经被删除', U('tuan/index'));
        }
        if ($detail['user_id'] != $this->uid) {
            $this->error('该团购不存在或者已经被删除', U('tuan/index'));
        }
        if ($detial['closed'] == 1) {
            //如果是关闭状态，防止刷分
            $this->error('该团购状态不能执行此操作', U('tuan/index'));
        }
        if ($detail['status'] != 0) {
            $this->error('该团购不存在或者已经被删除', U('tuan/index'));
        }
        //先返还积分才行
        if ($obj->save(array('order_id' => $order_id, 'closed' => 1))) {
            D('Users')->addIntegral($detail['user_id'], $detail['use_integral'], '取消抢购订单' . $detail['order_id'] . '积分退还');
        }
        $this->success('取消订单成功!', U('tuan/index'));
    }
    public function orderloading()
    {
        $Tuanorder = D('Tuanorder');
        import('ORG.Util.Page');
        // 导入分页类
        $map = array('user_id' => $this->uid, 'closed' => 0);
        //这里只显示 实物
        if (isset($_GET['aready']) || isset($_POST['aready'])) {
            $aready = (int) $this->_param('aready');
            if ($aready != 999) {
                $map['status'] = $aready;
            }
            $this->assign('aready', $aready);
        } else {
            $this->assign('aready', 999);
        }
        $count = $Tuanorder->where($map)->count();
        // 查询满足要求的总记录数
        $Page = new Page($count, 10);
        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();
        // 分页显示输出
        $var = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        $p = $_GET[$var];
        if ($Page->totalPages < $p) {
            die('0');
        }
        $list = $Tuanorder->where($map)->order(array('order_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $tuan_ids = array();
        foreach ($list as $k => $val) {
            $tuan_ids[$val['tuan_id']] = $val['tuan_id'];
        }
        //查询商家
        $shop_ids = array();
        foreach ($list as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('tuans', D('Tuan')->itemsByIds($tuan_ids));
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        //查询商家名字
        $this->assign('list', $list);
        // 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page', $show);
        // 赋值分页输出
        $this->display();
        // 输出模板
    }
    public function detail($order_id)
    {
        $order_id = (int) $order_id;
        if (empty($order_id) || !($detail = D('Tuanorder')->find($order_id))) {
            $this->error('该订单不存在');
        }
        if ($detail['user_id'] != $this->uid) {
            $this->error('请不要操作他人的订单');
        }
        if (!($dianping = D('Tuandianping')->where(array('order_id' => $order_id, 'user_id' => $this->uid))->find())) {
            $detail['dianping'] = 0;
        } else {
            $detail['dianping'] = 1;
        }
        $this->assign('tuans', D('Tuan')->find($detail['tuan_id']));
        $this->assign('detail', $detail);
        $this->display();
    }
    public function dianping($order_id)
    {
        $order_id = (int) $order_id;
        if (empty($order_id) || !($detail = D("Tuanorder")->find($order_id))) {
            $this->error("该订单不存在");
        }
        if (!($tc = D("Tuancode")->where(array("order_id" => $order_id, "is_used" => 1))->find())) {
            $this->error("您的抢购券还没有使用");
        }
        if ($detail['user_id'] != $this->uid) {
            $this->error("请不要操作他人的订单");
        }
        if ($detail['is_dianping'] != 0) {
            $this->error("您已经点评过了");
        }
        if ($this->isPost()) {
            $data = $this->checkFields($this->_post("data", FALSE), array("score", "cost", "contents"));
            $data['user_id'] = $this->uid;
            $data['order_id'] = $detail['order_id'];
            $data['shop_id'] = $detail['shop_id'];
            $data['tuan_id'] = $detail['tuan_id'];
            $data['score'] = (int) $data['score'];
            if ($data['score'] <= 0 || 5 < $data['score']) {
                $this->fengmiMsg("请选择评分");
            }
            $data['cost'] = (int) $data['cost'];
            $data['contents'] = htmlspecialchars($data['contents']);
            if (empty($data['contents'])) {
                $this->fengmiMsg("不说点什么么");
            }
            $data['create_time'] = NOW_TIME;
            $data_tuan_dianping = $this->_CONFIG['mobile']['data_tuan_dianping'];
            $data['show_date'] = date('Y-m-d', NOW_TIME + $data_tuan_dianping * 86400);
            //15天生效
            $data['create_ip'] = get_client_ip();
            $obj = d("Tuandianping");
            if ($dianping_id = $obj->add($data)) {
                $photos = $this->_post("photos", FALSE);
                $local = array();
                foreach ($photos as $val) {
                    if (isimage($val)) {
                        $local[] = $val;
                    }
                }
                if (!empty($local)) {
                    D("Tuandianpingpics")->upload($order_id, $local);
                }
                D("Tuanorder")->save(array("order_id" => $order_id, "is_dianping" => 1));
                D("Shop")->updateCount($detail['shop_id'], "score_num");
                D("Users")->updateCount($this->uid, "ping_num");
                D("Users")->prestige($this->uid, "dianping");
                $this->fengmiMsg("评价成功", U("member/index"));
            }
            $this->fengmiMsg("操作失败！");
        } else {
            $this->assign("detail", $detail);
            $tuan = D("Tuan")->find($detail['tuan_id']);
            $this->assign("tuan", $tuan);
            $this->display();
        }
    }
}