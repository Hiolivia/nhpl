<?php



class CommunityModel extends CommonModel {

    protected $pk = 'community_id';
    protected $tableName = 'community';
    protected $orderby = array('orderby' => 'asc');

    public function _format($data) {
        static $area = null;
        if ($area == null) {
            $area = D('Area')->fetchAll();
        }
        $data['area_name'] = $area[$data['area_id']]['area_name'];
        return $data;
    }

    
}
