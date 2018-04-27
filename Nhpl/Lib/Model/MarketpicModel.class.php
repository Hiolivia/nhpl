<?php



class MarketpicModel extends CommonModel{
    protected $pk   = 'pic_id';
    protected $tableName =  'market_pic';
    
    public function upload($market_id,$photos){
        $market_id = (int)$market_id;
        $this->delete(array("where"=>array('market_id'=>$market_id)));
        foreach($photos as $val){
            $this->add(array('pic'=>$val,'market_id'=>$market_id));
        }
        return true;
    }
    
   
    
    public function getPics($market_id){
        $market_id = (int)$market_id;
        return $this->where(array('market_id'=>$market_id))->select();
    }
}