<?php

namespace models;

use Basic\BasicModel;

class Config extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{config}}';

    public function attribute()
    {
        return [
            'id'           => 'ID',
            'config_type'  => '类型',
            'config_name'  => 'name',
            'config_value' => 'value',
        ];
    }
}