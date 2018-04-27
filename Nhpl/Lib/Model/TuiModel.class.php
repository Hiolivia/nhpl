<?php



class TuiModel extends CommonModel{
    protected $pk   = 'tui_id';
    protected $tableName =  'tui';
    protected $token = 'bao_tui';
    
    public function fetchAll(){
        $datas = $this->select();
        $return = array();
        foreach($datas as $k=>$v){
            $return[$v['tui_link']] = $v['tui_name'];
        }
        return $return;
    }
    
}