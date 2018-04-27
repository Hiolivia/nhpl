<?php



class ZhuanconfigModel extends CommonModel{
    protected $pk         = 'config_id';
    protected $tableName  = 'zhuan_config';
    protected $token      = 'zhuan_config';
  
    protected $_validate = array(
    	array('color_title','/^#(\d|[A-Fa-f]){6}$/','PC背景颜色不合法,如#FFFFFF'    ,Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    	array('color_bg',   '/^#(\d|[A-Fa-f]){6}$/','PC标题颜色不合法,如#FFFFFF'    ,Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    	array('color_mtitle','/^#(\d|[A-Fa-f]){6}$/','手机背景值不合法,如#FFFFFF' ,Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    	array('color_mbg',   '/^#(\d|[A-Fa-f]){6}$/','手机标题值不合法,如#FFFFFF' ,Model::MUST_VALIDATE,'regex',Model::MODEL_BOTH),
    	array('title',  '5,255','专题页面标题不能为空',Model::MUST_VALIDATE,'length',Model::MODEL_BOTH),
    );
   
}