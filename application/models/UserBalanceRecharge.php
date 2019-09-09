<?php

namespace models;

use Basic\BasicModel;

class UserBalanceRecharge extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{user_balance_recharge}}';

    public function attribute()
    {
        return [
            'id'              => 'ID',
            'uid'             => '报销者',
            'to_uid'          => '财务大人',
            'amount'          => '充值金额',
            'recharge_status' => '状态',
            'create_at'       => '创建时间',
            'update_at'       => '更新时间'
        ];
    }
}