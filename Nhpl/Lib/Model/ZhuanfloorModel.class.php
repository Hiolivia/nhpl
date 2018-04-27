<?php



class ZhuanfloorModel extends CommonModel {

    protected $pk         = 'floor_id';
    protected $tableName  = 'zhuan_floor';
    protected $token      = 'zhuan_floor';

    protected $_validate = array(
    	array('title','2,15','楼层名称2至15个字符'  ,Model::MUST_VALIDATE,'length',Model::MODEL_BOTH),
    	array('sort' ,'/^\d{1,}$/','排序值不合法',Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH)
    );


}
