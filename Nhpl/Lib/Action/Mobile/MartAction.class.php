<?php
class MartAction extends CommonAction{
    public function _initialize(){
        parent::_initialize();
        $this->_cart = $this->getcart();
		$Goods = D('Goods');
		$weidiancates = D('Weidiancate')->fetchAll();
	    foreach ($weidiancates as $key => $v) {
           if ($v['cate_id']) {
            $catids = D('Weidiancate')->getChildren($v['cate_id']);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
        }
              $count = $Goods->where($map)->count(); // 统计当前分类记录
              $weidiancates[$key]['count'] = $count;
        }
        $this->assign('weidiancates',$weidiancates);
		//结束
		
    }
	
	public function main() {
        $map = array('audit' => 1, 'closed' => 0, 'end_date' => array('EGT', TODAY));
        $order = (int) $this->_param('order');
        switch ($order) {
            case 2:
                $orderby = array('sold_num' => 'desc');
                break;
            case 3:
                $orderby = array('goods_id' => 'desc');
                break;
            default:
                $orderby = array('mall_price' => 'asc' ,'orderby' => 'asc' );
                break;
        }
        $this->assign('order',$order);
        $list = D('Weidian')->order($orderby)->where($map)->limit(0, 10)->select();
        $this->assign('list',$list);
        $this->display();
    }
	
    public function getcart( ){
        $id = (int)$this->_param("id");
        $wd = D("WeidianDetails")->find( $id );
        $cart = (array)json_decode($_COOKIE['mall'] );
        $carts = array();
        foreach ($cart as $kk => $vv){
            foreach ($vv as $key => $v){
                $v =(array)$v;
                $carts[$kk][$key] = $v;
                if ($v['num'] == 0 ){
                    unset( $Var_168[$key] );
                }
            }
        }
        $ids = $nums = array( );
        foreach ($carts[$wd['shop_id']] as $k => $val ){
            $ids[$val['goods_id']] = $val['goods_id'];
            $nums[$val['goods_id']] = $val['num'];
        }
        $goods = D("Goods")->itemsByIds($ids);
        foreach ($goods as $k => $val){
            $goods[$k]['cart_num'] = $nums[$val['goods_id']];
            $goods[$k]['total_price'] = $nums[$val['goods_id']] * $val['mall_price'];
        }
        return $goods;
    }

    public function index(){
        $cat = (int)$this->_param("cat");
        $this->assign("cat", $cat);
		$order = $this->_param('order','htmlspecialchars');
		$this->assign('order', $order);
		
		$area_id = (int) $this->_param('area_id');
        $this->assign('area_id', $area_id);
        
        $this->assign("nextpage", linkto("mart/loaddata", array('cat'=>$cat,'area_id'=>$area_id,'order'=>$order,"t" => NOW_TIME,"p" => "0000")));
        $this->display();
    }

    public function loaddata(){
        $weidian = d( "WeidianDetails" );
        import( "ORG.Util.Page" );
        $map = array("audit" => 1,'closed' => 0,"city_id" => $this->city_id );
		$cat = (int) $this->_param('cat');
        $cates = D('Weidiancate')->fetchAll();
        if ($cates[$cat]){
            $catids = D('Weidiancate')->getChildren($cat);
            if (!empty($catids)) {
                $map['cate_id'] = array('IN', $catids);
            } else {
                $map['cate_id'] = $cat;
            }
            $this->assign('parent_id', $cates[$cat]['parent_id'] == 0 ? $cates[$cat]['cate_id'] : $cates[$cat]['parent_id']);
            $this->seodatas['cate_name'] = $cates[$cat]['cate_name'];
        }
        $this->assign('cat', $cat);
		
		$area_id = (int) $this->_param('area_id');
        if ($area_id) {
            $map['area_id'] = $area_id;
        }
		
		
		//微店二级分类结束
        $lat = addslashes(cookie("lat"));
        $lng = addslashes(cookie("lng"));
        if (empty($lat) || empty($lng)){
            $lat = $this->city['lat'];
            $lng = $this->city['lng'];
        }
        $count = $weidian->where($map)->count();
        $Page = new Page($count, 8);
        $show = $Page->show( );
        $var = C("VAR_PAGE")? C("VAR_PAGE") : "p";
        $p = $_GET[$var];
        if ($Page->totalPages <$p){
            exit( "0" );
        }
	    //排序重写
	    $order = $this->_param('order','htmlspecialchars');
        switch ($order) {
            case 2:
                $orderby = array('views' => 'desc');
                break;
            default:
                 $orderby = array('yuyue_num' => 'desc');
                break;
        }
		 
        $list = $weidian->order("(ABS(lng - '".$lng."') + ABS(lat - '{$lat}')) asc" )->where($map)->limit( $Page->firstRow.",".$Page->listRows )->select();
        foreach ( $list as $k => $val ){
            $list[$k]['d'] = getdistance( $lat, $lng, $val['lat'], $val['lng'] );
        }
        $shop_ids = array();
        foreach ($list as $key => $v){
            $shop_ids[$v['shop_id']] = $v['shop_id'];
        }
        $shopdetails = D("Shopdetails")->itemsByIds($shop_ids);
        foreach ($list as $k => $val){
            $list[$k]['price'] = $shopdetails[$val['shop_id']]['price'];
        }
        $this->assign("linkArr",$linkArr);
        $this->assign("list", $list );
        $this->assign("page", $show );
        $this->display();
    }

    public function lists(){
        $id =(int)$this->_get("id");
        $wd = D("WeidianDetails")->find($id);
        if (!($detail = D("WeidianDetails")->find($id))){
            $this->error("没有该微店商家");
            exit();
        }
        if ($detail['audit'] != 1 ){
            $this->error("没有该微店商家");
            exit();
        }
        $autocates = D("Goodsshopcate" )->order(array("orderby" => "asc") )->where(array("shop_id" => $wd['shop_id']))->select();
        $Goods = D("Goods");
        $map = array("closed" => 0,"audit" => 1,"city_id" => $this->city_id,"shop_id" => $wd['shop_id'],"end_date" => array("EGT",TODAY));
		
		if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title'] = array('LIKE', '%' . $keyword . '%');
        }
		
        $list = $Goods->order(array( "sold_num" =>"desc","goods_id" =>"desc"))->where($map)->select();
        foreach ($list as $k => $val ){
            $list[$k]['cart_num'] = $this->_cart[$val['goods_id']]['cart_num'];
        }
        $this->assign("list", $list);
        $this->assign("autocates", $autocates);
        $this->assign("detail", $detail);
        $this->display();
    }

    public function detail($goods_id){
        $goods_id = (int)$goods_id;
        $detail = D("Goods")->find($goods_id);
        $this->assign("detail", $detail);
        $this->display();
    }

    public function cart(){
        $id = (int)$this->_param("id");
        if ( empty($id) ){
            $this->error( "参数不正确" );
        }
        $detail = D( "WeidianDetails" )->find( $id );
        if ( empty( $detail ) ){
            $this->error( "微店不存在" );
        }
        $goods = $this->_cart;
        if ( empty( $goods ) ){
            $this->error( "亲还没有选购产品呢!", U	( "mart/lists", array("id" => $id)));
        }
        $this->assign( "cart_goods", $goods );
        $this->assign( "detail", $detail );
        $this->display( );
    }
  
    public function shopdetail( ){
        $id = ( integer )$this->_param( "id" );
        if ( !( $wshop = D( "WeidianDetails" )->find( $id ) ) ){
            $this->error( "没有该微店商家" );
            exit( );
        }
        if ( $wshop['closed'] != 0 || $wshop['audit'] != 1 ){
            $this->error( "该微店商家不存在" );
            exit( );
        }
        if ( !( $detail = D( "Shop" )->find( $wshop['shop_id'] ) ) ){
            $this->error( "没有该商家" );
            exit( );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            $this->error( "该商家不存在" );
            exit( );
        }
        $this->assign( "wshop", $wshop );
        $this->assign( "detail", $detail );
        $this->assign( "ex", D( "Shopdetails" )->find( $wshop['shop_id'] ) );
        $this->display( );
    }

    public function dianping( ){
        $id = ( integer )$this->_param( "id" );
        if ( !( $wshop = D( "WeidianDetails" )->find( $id ) ) ){
            $this->error( "没有该微店商家" );
            exit( );
        }
        if ( $wshop['closed'] != 0 || $wshop['audit'] != 1 ){
            $this->error( "该微店商家不存在" );
            exit( );
        }
        if ( !( $detail = D( "Shop" )->find( $wshop['shop_id'] ) ) ){
            $this->error( "没有该商家" );
            exit( );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            $this->error( "该商家不存在" );
            exit( );
        }
        $this->assign( "wshop", $wshop );
        $this->assign( "detail", $detail );
        $this->display( );
    }

    public function dianpingloading( ){
        $id = ( integer )$this->_get( "id" );
        if ( !( $wshop = D( "WeidianDetails" )->find( $id ) ) ){
            exit( "0" );
        }
        if ( $wshop['closed'] != 0 || $wshop['audit'] != 1 ){
            exit( "0" );
        }
        if ( !( $detail = D( "Shop" )->find( $wshop['shop_id'] ) ) ){
            exit( "0" );
        }
        if ( $detail['closed'] != 0 || $detail['audit'] != 1 ){
            exit( "0" );
        }
        $shopdianping = D( "Shopdianping" );
        import( "ORG.Util.Page" );
        $map = array("closed" => 0,"shop_id" => $detail['shop_id'],"show_date" => array("ELT",TODAY ));
        $count = $shopdianping->where( $map )->count( );
        
        $Page = new Page( $count, 5 );
        $var = c( "VAR_PAGE" ) ? c( "VAR_PAGE" ) : "p";
        $p = $_GET[$var];
        if ( $Page->totalPages < $p ){exit( "0" );}
        $show = $Page->show( );
        $list = $shopdianping->where( $map )->order( array( "dianping_id" => "desc" ) )->limit( $Page->firstRow.",".$Page->listRows )->select( );
        $user_ids = $dianping_ids = array( );
        foreach ( $list as $k => $val ){
            $list[$k] = $val;
            $user_ids[$val['user_id']] = $val['user_id'];
            $dianping_ids[$val['dianping_id']] = $val['dianping_id'];
        }
        if ( !empty( $user_ids ) ) {
            $this->assign( "users", D( "Users" )->itemsByIds( $user_ids ) );
        }
        if ( !empty( $dianping_ids ) ){
            $this->assign( "pics", D( "Shopdianpingpics" )->where( array("dianping_id" => array("IN", $dianping_ids) ) )->select( ) );
        }
        $this->assign( "totalnum", $count );
        $this->assign( "list", $list );
        $this->assign( "detail", $detail );
        $this->assign( "wshop", $wshop );
        $this->display( );
    }

}

