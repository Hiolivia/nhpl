<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class UserweixinModel extends CommonModel{
    protected $pk   = 'wx_id';
    protected $tableName =  'user_weixin';


	public function detail_by_unionid()
	{

		if($row = D('Userweixin')->query("SELECT w.*,m.* FROM bao_user_weixin w LEFT JOIN bao_users m ON m.user_id=w.user_id WHERE w.unionid='$unionid'")){
            return $row;
        }
        return false;
	}

	public function detail_by_openid($openid)
    {
        if($row = D('Userweixin')->query("SELECT w.*,m.* FROM bao_user_weixin w LEFT JOIN bao_users m ON  m.user_id=w.user_id WHERE w.openid='$openid'")){
            return $row;
        }
        return false;
    }
    
}