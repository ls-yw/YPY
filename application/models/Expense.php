<?php

namespace models;

use Basic\BasicModel;

class Expense extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{expense}}';

    public function attribute()
    {
        return [
            'id'          => 'ID',
            'uid'         => '报销者',
            'to_uid'      => '财务大人',
            'cate_id'     => '分类',
            'title'       => '报销标题',
            'content'     => '报销内容',
            'deleted'     => '删除状态',
            'price'       => '报销金额',
            'at_type'     => '类型',
            'at_date'     => '费用日期',
            'at_status'   => '状态',
            'create_at' => '创建时间',
            'update_at' => '更新时间'
        ];
    }
}