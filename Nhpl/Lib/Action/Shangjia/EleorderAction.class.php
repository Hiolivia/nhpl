<?php

class EleorderAction extends CommonAction {

    protected $status = 0;
    protected $ele;

     public function _initialize() {
        parent::_initialize();
        $getEleCate = D('Ele')->getEleCate();
        $this->assign('getEleCate', $getEleCate);
        $this->ele = D('Ele')->find($this->shop_id);
        if (!empty($this->ele) && $this->ele['audit'] == 0) {
            $this->error("亲，您的申请正在审核中！");
        }
        if (empty($this->ele) && ACTION_NAME != 'apply') {
            $this->error('您还没有入住外卖频道', U('ele/apply'));
        }
        $this->assign('ele', $this->ele);
		$this->assign('eletypes', D('Eleorder')->getCfg());
      
    }
    
    
    public function index() {
        $this->status = 1;
        $this->showdata();
        $this->display(); // 输出模板
    }

    public function wait() {
        $this->status = 2;
        $this->showdata();
        $this->display(); // 输出模板
    }

    public function over() {
        $this->status = 8;
        $this->showdata();
        $this->display(); // 输出模板
    }
    
	
	public function whole() {
        $Eleorder = D( "Eleorder" );
        import( "ORG.Util.Page" );
        $map = array("closed" => 0,"shop_id" => $this->shop_id);
        if ( ( $bg_date = $this->_param( "bg_date", "htmlspecialchars" ) ) && ( $end_date = $this->_param( "end_date", "htmlspecialchars" ) ) ){
            $bg_time = strtotime( $bg_date );
            $end_time = strtotime( $end_date );
            $map['create_time'] = array(
                array("ELT",$end_time),
                array("EGT",$bg_time)
            );
            $this->assign( "bg_date", $bg_date );
            $this->assign( "end_date", $end_date );
        }
        else{
            if ( $bg_date = $this->_param( "bg_date", "htmlspecialchars" ) ){
                $bg_time = strtotime( $bg_date );
                $this->assign( "bg_date", $bg_date );
                $map['create_time'] = array("EGT",$bg_time);
            }
            if ( $end_date = $this->_param( "end_date", "htmlspecialchars" ) ){
                $end_time = strtotime( $end_date );
                $this->assign( "end_date", $end_date );
                $map['create_time'] = array("ELT",$end_time);
            }
        }
        if ( $keyword = $this->_param( "keyword", "htmlspecialchars" ) ){
            $map['order_id'] = array("LIKE","%".$keyword."%");
            $this->assign( "keyword", $keyword );
        }
        $count = $Eleorder->where( $map )->count( );
        
        $Page = new Page( $count, 25 );
        $show = $Page->show( );
        $list = $Eleorder->where( $map )->order( array( "order_id" => "desc" ) )->limit( $Page->firstRow.",".$Page->listRows )->select( );
		//自动确认收货不好使
        $config = d( "Setting" )->fetchAll( );
        $h = isset( $config['site']['ele'] ) ? ( integer )$config['site']['ele'] : 6;
        $t = NOW_TIME - $h * 3600;
		
        $user_ids = $order_ids = $addr_ids = array( );
        foreach ( $list as $key => $val ){
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
            if (!( $val['create_time'] < $t ) && !( $val['status'] == 2 ) ){
                $list[$key]['status'] = 8;//后期解决，但是资金不到账,这个是资金到账函数overOrder
				//D('Eleorder')->overOrder($val['order_id']);//打开这个页面怎么样解决此问题呢，如果是配置中，并存是件是对，那么要批量
            }
        }
        if ( !empty( $order_ids )){
            $goods = d("Eleorderproduct")->where( array( "order_id" => array("IN",$order_ids )))->select();
            $goods_ids = array();
            foreach ($goods as $val){
                $goods_ids[$val['product_id']] = $val['product_id'];
            }
            $this->assign( "goods", $goods );
            $this->assign( "products", d( "Eleproduct" )->itemsByIds($goods_ids ));
        }
        $this->assign("addrs", d("Useraddr")->itemsByIds($addr_ids ));
        $this->assign("areas", d("Area")->fetchAll());
        $this->assign("business", d("Business")->fetchAll());
        $this->assign("list",$list);
        $this->assign("page",$show);
		$this->display();
    }
	
    
    public function count(){
        $dvo = D('DeliveryOrder'); // 实例化User对象
        $bg_date = strtotime(I('bg_date',0,'trim'));
        $end_date = strtotime(I('end_date',0,'trim'));
        $this->assign('btime',$bg_date);
        $this->assign('etime',$end_date);
        
        if($bg_date && $end_date){
            $pre_btime = date('Y-m-d H:i:s',$bg_date);
            $pre_etime = date('Y-m-d H:i:s',$end_date);
            $this->assign('pre_btime',$pre_btime);
            $this->assign('pre_etime',$pre_etime);
        }
        
        $map = array('shop_id'=>$this->shop_id,'type'=>1);
        if($bg_date && $end_date){
           $map['create_time'] = array('between',array($bg_date,$end_date)); 
        }
        
        import('ORG.Util.Page');// 导入分页类
        $count      = $dvo->where($map)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $list = $dvo->where($map)->order(array( "order_id" => "desc" ))->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集www.hatudou.com  二开开发qq  120585022
        $this->assign('page',$show);// 赋值分页输出

        $this->display();
        
    }
    
    
    function delivery_count(){
        
        $delivery_id = I('did',0,'intval,trim');
        $btime = I('btime',0,'trim');
        $etime = I('etime',0,'trim');
        $map = array();
        if($btime && $etime){
           $map['create_time'] = array('between',array($btime,$etime)); 
        }
        
        if(!$delivery_id || !($this->shop_id)){
            $this->ajaxReturn(array('status'=>'error','message'=>'错误'));
        }else{
            $map['delivery_id'] = $delivery_id;
            $map['shop_id'] = $this->shop_id;
            $map['type'] = 1;
            $count = D('DeliveryOrder') ->where($map)-> count();
            if($count){
                $this->ajaxReturn(array('status'=>'success','count'=>$count));
            }else{
                $this->ajaxReturn(array('status'=>'error','message'=>'错误'));
            }
        }
    }

    private function showdata() {
        $Eleorder = d( "Eleorder" );
        import( "ORG.Util.Page" );
        $map = array("closed" => 0,"shop_id" => $this->shop_id,"status" => $this->status);
        if ( ( $bg_date = $this->_param( "bg_date", "htmlspecialchars" ) ) && ( $end_date = $this->_param( "end_date", "htmlspecialchars" ) ) ){
            $bg_time = strtotime( $bg_date );
            $end_time = strtotime( $end_date );
            $map['create_time'] = array(
                array("ELT",$end_time),
                array("EGT",$bg_time)
            );
            $this->assign( "bg_date", $bg_date );
            $this->assign( "end_date", $end_date );
        }
        else{
            if ( $bg_date = $this->_param( "bg_date", "htmlspecialchars" ) ){
                $bg_time = strtotime( $bg_date );
                $this->assign( "bg_date", $bg_date );
                $map['create_time'] = array("EGT",$bg_time);
            }
            if ( $end_date = $this->_param( "end_date", "htmlspecialchars" ) ){
                $end_time = strtotime( $end_date );
                $this->assign( "end_date", $end_date );
                $map['create_time'] = array("ELT",$end_time);
            }
        }
        if ( $keyword = $this->_param( "keyword", "htmlspecialchars" ) ){
            $map['order_id'] = array("LIKE","%".$keyword."%");
            $this->assign( "keyword", $keyword );
        }
        $count = $Eleorder->where( $map )->count( );
        
        $Page = new Page( $count, 25 );
        $show = $Page->show( );
        $list = $Eleorder->where( $map )->order( array( "order_id" => "desc" ) )->limit( $Page->firstRow.",".$Page->listRows )->select( );
		//自动确认收货不好使
        $config = d( "Setting" )->fetchAll( );
        $h = isset( $config['site']['ele'] ) ? ( integer )$config['site']['ele'] : 6;
        $t = NOW_TIME - $h * 3600;
		
        $user_ids = $order_ids = $addr_ids = array( );
        foreach ( $list as $key => $val ){
            $user_ids[$val['user_id']] = $val['user_id'];
            $order_ids[$val['order_id']] = $val['order_id'];
            $addr_ids[$val['addr_id']] = $val['addr_id'];
            if (!( $val['create_time'] < $t ) && !( $val['status'] == 2 ) ){
                $list[$key]['status'] = 8;//后期解决，但是资金不到账,这个是资金到账函数overOrder
				//D('Eleorder')->overOrder($val['order_id']);//打开这个页面怎么样解决此问题呢，如果是配置中，并存是件是对，那么要批量确认收货？
            }
        }
        if ( !empty( $order_ids )){
            $goods = d("Eleorderproduct")->where( array( "order_id" => array("IN",$order_ids )))->select();
            $goods_ids = array();
            foreach ($goods as $val){
                $goods_ids[$val['product_id']] = $val['product_id'];
            }
            $this->assign( "goods", $goods );
            $this->assign( "products", d( "Eleproduct" )->itemsByIds($goods_ids ));
        }
        $this->assign("addrs", d("Useraddr")->itemsByIds($addr_ids ));
        $this->assign("areas", d("Area")->fetchAll());
        $this->assign("business", d("Business")->fetchAll());
        $this->assign("list",$list);
        $this->assign("page",$show);
    }
	
	
	
    public function queren($order_id) {
        $order_id = (int) $order_id;
        if (!$detail = D('Eleorder')->find($order_id)) {
            $this->baoError('没有该订单');
        }
        if ($detail['shop_id'] != $this->shop_id) {
            $this->baoError('您无权管理该商家');
        }
        if ($detail['status'] != 1) {
            $this->baoError('该订单状态不正确');
        }
        D('Eleorder')->save(array(
            'order_id' => $order_id,
            'status' => 2,
            'audit_time' => NOW_TIME
        ));
        D('Weixintmpl')->weixin_shop_delivery_user($order_id,$this->uid,1);//发货通知买家接口，1外卖，2商城，3家政
        $this->baoSuccess('已确认', U('eleorder/index'));
    }
	
	
	//确认订单
	  public function send($order_id) { 
		$order_id = (int) $order_id;
		//自动确认收货暂时不做
		$config = D('Setting')->fetchAll();
        $h = isset($config['site']['ele']) ? (int)$config['site']['ele'] : 6;
        $t = NOW_TIME - $h*3600;//这个时间是3天前的时间
    
        if (!$detail = D('Eleorder')->find($order_id)) {
            $this->baoError('没有该订单');
        }
        if ($detail['shop_id'] != $this->shop_id) {
            $this->baoError('您无权管理该商家');
        } 
		$shop = D('Shop')->find($detial['shop_id']);
			if ($shop['is_pei'] == 0) {
				   $DeliveryOrder = D('DeliveryOrder') -> where(array('type_order_id' =>$order_id,'type' =>1)) -> find();
					if (!empty($DeliveryOrder)) {
						$this->baoError('您开通了配送员配货，无权管理');
					}
			}else{//不走配送
				if (($detail['create_time'] <$t) && ($detail['status'] ==2)) {
					D('Eleorder')->overOrder($order_id);
					$this->baoSuccess('确认完成，资金已经结算到账户！', U('shangjia/eleorder/wait'));
			    }else{
					$this->baoError('操作失败');
				}
			
		     }
		 //强制执行资金到账
        
    }

}