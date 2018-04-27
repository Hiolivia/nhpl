<?php
class GoodsModel extends CommonModel{
    protected $pk   = 'goods_id';
    protected $tableName =  'goods';
	 protected $_validate = array(
        array( ),
        array( ),
        array( )
    );
	public function getError() {
        return $this->error;
    }

   //万能检测库存接口
    public function check_goods_id_mum($order_ids) {
		
        if (is_array($order_ids)) {
            $order_ids = join(',', $order_ids);
            $Order = D('Order')->where("order_id IN ({$order_ids})")->select();
            foreach ($Order as $k => $v) {
                if (false == $this->check_goods_stock($v['order_id'])) {
					return false;
				}else{
					return TRUE;  
				}
            }
        } else {
            $order_ids = (int) $order_ids;
            $Order = D('Order')->where('order_id =' . $order_ids)->select();
            foreach ($Order as $k => $v) {
			   if (false == $this->check_goods_stock($v['order_id'])) {
					return false;
				}else{
					return TRUE;  
				}

            }
        }
       
    }
	

    //付款前检测库存
    public function check_goods_stock($order_id){
		
        $order_id = (int) $order_id;
        $ordergoods_ids = D('Ordergoods')->where(array('order_id' => $order_id))->select();
        foreach ($ordergoods_ids as $k => $v) {
            $goods_num = D('Goods')->where(array('goods_id' => $v['goods_id']))->find();
            if ($goods_num['num'] < $v['num']) {
				$this->error = '商品名称【' . $goods_num['title'] . '】库存不足'.$v['num'].'/'.$goods_num['guige'].'无法付款，请重新下单';
				return false;
            }
			return TRUE;
        }
		 return TRUE;
    }


    public function _format($data){
        $data['save'] =  round(($data['price'] - $data['mall_price'])/100,2);
        $data['price'] = round($data['price']/100,2);
		$data['is_agent_price'] = round($data['is_agent_price']/100,2);
		$data['cost_price'] = round($data['cost_price']/100,2);
		$data['factory_price'] = round($data['factory_price']/100,2);
		$data['wholesale_price'] = round($data['wholesale_price']/100,2);
		$data['market_price'] = round($data['market_price']/100,2);
		$data['retail_price'] = round($data['retail_price']/100,2);
		$data['mobile_fan'] = round($data['mobile_fan']/100,2);
        $data['mall_price'] = round($data['mall_price']/100,2); 
        $data['settlement_price'] = round($data['settlement_price']/100,2); 
        $data['commission'] = round($data['commission']/100,2); 
        $data['discount'] = round($data['mall_price'] * 10 / $data['price'],1);
        return $data;
    }

}