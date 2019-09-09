<?php

namespace models;

use Basic\BasicModel;

class UserBalance extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{user_balance}}';

    public function attribute()
    {
        return [
            'id'          => 'ID',
            'uid'         => '报销者',
            'to_uid'      => '财务大人',
            'balance'     => '余额',
            'create_at' => '创建时间',
            'update_at' => '更新时间'
        ];
    }
}