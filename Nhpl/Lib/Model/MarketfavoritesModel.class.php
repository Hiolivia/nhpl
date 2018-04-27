<?php



class MarketfavoritesModel extends CommonModel{
    protected $pk   = 'favorites_id';
    protected $tableName =  'market_favorites';
    
    
    public function check($market_id,$user_id){
        $data = $this->find(array('where'=>array('market_id'=>(int)$market_id,'user_id'=>(int)$user_id)));
        return $this->_format($data);
    }
}