<?php

/* 
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class HotelroomModel extends CommonModel{
    protected $pk   = 'room_id';
    protected $tableName =  'hotel_room';
    
    
    public function getRoomType(){
        return array(
            1 => '双床房',
            2 => '单人房',
            3 => '大床房',
            4 => '无烟房',
        );
    }
     
}