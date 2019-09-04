<?php

namespace models;

use Basic\BasicModel;

class User extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{user}}';

    public function attribute()
    {
        return [
            'id'         => 'ID',
            'realname'   => '姓名',
            'mobile'     => '手机号',
            'password'   => '密码',
            'balance'    => '余额',
            'is_deleted' => '是否删除',
            'create_at'  => '创建时间',
            'update_at'  => '更新时间',
        ];
    }
}