<?php



class FavoritesAction extends CommonAction {

   public function index() {
        $Shopfavorites = D('Shopfavorites');
        import('ORG.Util.Page');
        $map = array('user_id' => $this->uid);
        $count = $Shopfavorites->where($map)->count();
        $Page = new Page($count, 16);
        $show = $Page->show();
        $list = $Shopfavorites->where($map)->order('favorites_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $shop_ids = array();
        foreach ($list as $k => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->assign('prices', D('Shopdetails')->itemsByIds($shop_ids));
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function deletefavo($favorites_id) {
        $favorites_id = (int) $favorites_id;
        if ($detial = D('Shopfavorites')->find($favorites_id)) {
            if ($detial['user_id'] == $this->uid) {
                D('Shopfavorites')->delete($favorites_id);
                $this->baoSuccess('取消关注成功!', U('favorites/index'));
            }
        }
        $this->baoError('参数错误');
    }

    
}
