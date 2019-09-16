<?php

namespace models;

use Basic\BasicModel;

class Category extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{category}}';

    public function attribute()
    {
        return [
            'id'        => 'ID',
            'name'      => '名称',
            'type'      => '类型',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];
    }
}