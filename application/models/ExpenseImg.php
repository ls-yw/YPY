<?php

namespace models;

use Basic\BasicModel;

class ExpenseImg extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{expense_img}}';

    public function attribute()
    {
        return [
            'id'          => 'ID',
            'expense_id'  => '',
            'uid'         => '用户ID',
            'img_url'     => '图片地址',
            'at_type'     => '类型',
            'deleted'     => '删除状态',
            'create_at' => '创建时间',
            'update_at' => '更新时间'
        ];
    }
}