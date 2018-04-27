<?php



class CityModel extends CommonModel{
    protected $pk   = 'city_id';
    protected $tableName =  'city';
    protected $token = 'city';
    protected $orderby = array('orderby'=>'asc');
   
    public function setToken($token)
    {
        $this->token = $token;
    }
 
}