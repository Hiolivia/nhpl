<?php

class ScheduleAction extends CommonAction{

    //发型师端日程管理
    public function schedule(){
        if ($hu_id = getUid()){
            //可预约天数
            $config = D('ScheduleConfig')->order('sc_id')->limit(1)->select();
            $day = $config ? $config[0]['day'] : 7;

            $arr = $result = [];
            //可预约时间
            $schedule = D('Schedule')->where(['closed'=>0])->order('time')->select();
            foreach ($schedule as $row){
                $arr[$row['time']] = 0;
            }

            $weekarray=array("日","一","二","三","四","五","六");

            for($i = 0;$i < $day;$i++){
                $time = strtotime("+ $i day",time());

                if(!$i){
                    $desc = '今天';
                }elseif($i == 1){
                    $desc = '明天';
                }elseif($i == 2){
                    $desc = '后天';
                }else{
                    $desc = '星期'.$weekarray[date('w',$time)];
                }

                $result[] = [
                    'day' => date('Y-m-d',$time),
                    'desc' => $desc,
                    'schedule' => $arr
                ];
            }

            //发型师预约记录
            // ---根据用户端预约数据进行提取

            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok',
                'result' => $result
            ]);
        }else{
            $data = [
                'status' => self::BAO_REG_NO_FIND,
                'msg' =>'请先登录再操作'
            ];
            $this->stringify($data);
        }
    }

}