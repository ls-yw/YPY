<?php

namespace models;

use Basic\BasicModel;

class UserRelation extends BasicModel
{
    /**
     * 表名
     *
     * @var string
     */
    protected $_targetTable = '{{user_relation}}';

    public function attribute()
    {
        return [
            'id'              => 'ID',
            'uid'             => '报销人ID',
            'to_uid'          => '财务大人ID',
            'relation_status' => '状态',
            'create_at'       => '创建时间',
            'update_at'       => '更新时间',
        ];
    }
}