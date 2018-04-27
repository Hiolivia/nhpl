<?php



class ZhuantiAction extends CommonAction {

    public function index($zt=1) {
        $maps   = D('Zhuanmap')->where(array('status'=>1))->getField('map_id',true);
        if(!in_array($zt, $maps)){
            $this->error('查询类容不存在！');
        }
        $config = D('Zhuanconfig')->where(array('status'=>1,'map_id'=>$zt))->find();
        $floors = D('Zhuanfloor')->where("status=1")->select();
        $list   = D('Zhuan')->where(array('map_id'=>$zt,'deadline'=>array('gt',time())))->order('sort asc')->select();
        $ids    = implode(',',array_map(function($item){
                    return $item['goods_id'];
                  },$list));
        $map    = array('audit'=>1,'closed'=>0);
        $goods  = M('Tuan')->where($map)->select($ids);
        $this->assign('config',$config);
        $this->assign('goods',$goods);
        $this->display();
    }
}