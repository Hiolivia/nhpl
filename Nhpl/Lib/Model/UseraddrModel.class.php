<?php
class UseraddrModel extends CommonModel{
    protected $pk   = 'addr_id';
    protected $tableName =  'user_addr';
	//获取用户默认地址
	public function get_user_addr_is_default($user_id){
		$useraddr_is_default = D('Useraddr')->where(array('user_id' => $user_id, 'is_default' => 1))->limit(0, 1)->select();
        $useraddrs = D('Useraddr')->where(array('user_id' => $user_id))->limit(0, 1)->select();
        if (!empty($useraddr_is_default)) {
			return $useraddr_is_default;
        } else {
            return $useraddrs;
        }
    }
    
}