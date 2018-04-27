<?php

class OrdergoodsModel extends CommonModel {
    protected $pk = 'id';
    protected $tableName = 'order_goods';
    protected $types = array(
        0 => '等待发货',
        1 => '已经捡货',
        8 => '已完成配送',
    );

    public function getType() {
        return $this->types;
    }

}