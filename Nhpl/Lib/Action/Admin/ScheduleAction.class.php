<?php

class ScheduleAction extends CommonAction{
    private $create_fields = ['time'];
    private $edit_fields = ['time'];

    public function index(){
        import('ORG.Util.Page');
        $schedule = D('Schedule');
        $count = $schedule->count();
        $Page = new Page($count, 25);
        $show = $Page->show();
        $list = $schedule->order(array('time' => 'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $day = D('ScheduleConfig')->find();

        $this->assign('day', !empty($day)?$day['day']:7);

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Schedule');
            if($obj->add($data)){
//                $obj->cleanCache();
                $this->baoSuccess('添加成功',U('schedule/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    public function edit($schedule_id = 0){
        if ($schedule_id){
            $obj = D('Schedule');
            $schedule = $obj->find($schedule_id);

            if ($this->isPost()){
                $data = $this->createCheck();
                $data['schedule_id'] = $schedule_id;
                if($obj->save($data) !== false){
//                    $obj->cleanCache();
                    $this->baoSuccess('操作成功',U('schedule/index'));
                }
                $this->baoError('操作失败');
            }else{
                $this->assign('detail',$schedule);
                $this->display();
            }
        }else{
            $this->baoError('请选择要编辑的预约时间！');
        }
    }

    public function change($schedule_id = 0){
        if ($schedule_id){
            $obj = D('Schedule');
            $schedule = $obj->find($schedule_id);
            $data = ['schedule_id'=>$schedule_id,'closed'=>$schedule['closed']?0:1];
            if ($obj->save($data)){
                $this->baoSuccess('操作成功',U('schedule/index'));
            }
            $this->baoError('操作失败');
        }else{
            $this->baoError('请选择要操作的预约时间！');
        }
    }

    public function editday(){
        if ($this->isPost()) {
            $data = $this->_post('data', false);
            if (!is_numeric($data['day']) || empty($data['day'])){
                $this->baoError('预约周期参数错误！');
            }else{
                $obj = D('ScheduleConfig');
                $info = $obj->limit(1)->select();

                if ($info){
                    $data['sc_id'] = $info[0]['sc_id'];
                    $obj->save($data);
                }else{
                    $obj->add($data);
                }
            }

            $this->baoSuccess('修改成功');
        }
    }

    private function createCheck()
    {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        if (empty($data['time'])) {
            $this->baoError('请输入可预约时间');
        }

        $data['time'] = htmlspecialchars($data['time'], ENT_QUOTES, 'UTF-8');
        return $data;
    }
}