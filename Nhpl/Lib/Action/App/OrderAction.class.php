<?php

class OrderAction extends CommonAction{
    //---------发型师端

    //扫一扫
    public function orderScan(){
        $code = $_POST['code'];
    }

    //券码核销
    public function orderVerify(){
        $code = $_POST['code'];

        if ($huser_id = getUid()){
            if ($code){
                //检验订单券码有效性

                //对应订单状态有效性  (订单各状态待确定!)

                //各条件检验有效即变更订单状态  若使用了优惠券则变更用户优惠券领用信息is_used

                $status = self::BAO_REQUEST_SUCCESS;
                $msg = 'ok';
            }else{
                $status = self::BAO_DETAIL_NO_EXSITS;
                $msg = '请输入有效订单券码';
            }
        }else{
            $status = self::BAO_REG_NO_FIND;
            $msg = '请先登录再操作';
        }

        $this->stringify([
            'status' => isset($status)?$status:0,
            'msg' => isset($msg)?$msg:''
        ]);
    }
}