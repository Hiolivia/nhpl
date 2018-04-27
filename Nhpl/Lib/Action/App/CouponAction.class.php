<?php

class CouponAction extends CommonAction{
    //---------发型师端

    //面对面发券
    public function hcoupon(){
        if ($hu_id = getUid()){
            $huser = D('Huser')->find($hu_id);
            $store_id = isset($huser['store_id']) ? $huser['store_id'] : 0;//所属门店

            $obj = D('Coupon');
            //$coupons = $obj->where(array('remains'=>array('>',0),'audit'=>1,'closed'=>0))->select();
            $coupons = $obj->where('(store_id = 0 or store_id = '.$store_id.' ) and remains > 0 and audit = 1 and closed = 0 and expire_date>='.date('Y-m-d'))
            ->order('coupon_id desc')->select();

            foreach ($coupons as &$coupon){
                $coupon['type_desc'] = $coupon['type'] ? '满折券' : '满减券';
                $coupon['full_price'] = round($coupon['full_price']/100,2);
                $coupon['reduce_price'] = round($coupon['reduce_price']/100,2);

                //-----优惠券适用范围待完善
                $coupon['desc'] = '';

            }

            $this->stringify([
                'status' => self::BAO_REQUEST_SUCCESS,
                'msg' => 'ok',
                'result' => $coupons
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