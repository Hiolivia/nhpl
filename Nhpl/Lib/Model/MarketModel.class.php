<?php



class MarketModel extends CommonModel{
    protected $pk   = 'market_id';
    protected $tableName =  'market'; //数据表结构的修改主要是为了兼容之前的
    
    public function getType(){
        
        return  array(
            1   => '美食',
            2   => '购物',
            3   => '电影',
            4   => '亲子',
            5   => '休闲娱乐',
            6   => '超市',
        );
    }
    
    
}