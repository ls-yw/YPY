<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%expense}}".
 *
 * @property int $id
 * @property int $uid 报销者
 * @property int $to_uid 财务大人
 * @property int $cate_id 分类
 * @property string $title 报销标题
 * @property string $content 报销内容
 * @property int $deleted 删除状态 0正常 1删除
 * @property string $price 报销金额
 * @property string $at_date 费用日期
 * @property int $at_status 状态 1 待审核 2待打款 3待确认 4已完成 5已取消 6已拒绝
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class Expense extends \yii\db\ActiveRecord
{
    
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                #设置默认值
                'value' => date('Y-m-d H:i:s'),
            ]
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%expense}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'price', 'to_uid', 'at_date'], 'required'],
            [['uid', 'to_uid', 'cate_id', 'deleted', 'at_status'], 'integer'],
            [['price'], 'number'],
            [['at_date', 'create_time', 'update_time'], 'safe'],
            [['title'], 'string', 'max' => 100],
            [['content'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '报销者',
            'to_uid' => '财务大人',
            'cate_id' => '分类',
            'title' => '报销标题',
            'content' => '报销内容',
            'deleted' => '删除状态 0正常 1删除',
            'price' => '报销金额',
            'at_date' => '费用日期',
            'at_status' => '状态 1 待审核 2待打款 3待确认 4已完成 5已取消 6已拒绝',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    public function getExpenseImg()
    {
        return $this->hasMany(ExpenseImg::className(), ['expense_id'=>'id']);
    }
}
