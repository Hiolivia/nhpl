<?php



class MarketdetailsModel extends CommonModel{
    protected $pk   = 'market_id';
    protected $tableName =  'market_details';
     public function upDetails($market_id,$data){
        $market_id = (int)$market_id;
        $data['market_id'] = $market_id;
        $rows = $this->find($market_id);
        if($rows){
            $this->save($data);
        }else{
            $this->add($data);
        }
        return true;
    }
    
    

    
}