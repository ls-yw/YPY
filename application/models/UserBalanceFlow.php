<?php

namespace models;

use Basic\BasicModel;

class UserBalanceFlow extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{user_balance_flow}}';

    public function attribute()
    {
        return [
            'id'        => 'ID',
            'uid'       => '报销者',
            'to_uid'    => '财务大人',
            'type'      => '变动方式',
            'amount'    => '变动金额',
            'balance'   => '余额',
            'create_at' => '创建时间',
            'update_at' => '更新时间'
        ];
    }
}