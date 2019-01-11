<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ypy_expense_img".
 *
 * @property int $id
 * @property int $expense_id
 * @property int $uid
 * @property string $img_url 图片地址
 * @property string $at_type
 * @property int $deleted 删除状态 0正常 1删除
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class ExpenseImg extends \yii\db\ActiveRecord
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
        return '{{%expense_img}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expense_id', 'uid', 'deleted'], 'integer'],
            [['at_type'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['img_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expense_id' => 'Expense ID',
            'uid' => 'Uid',
            'img_url' => 'Img Url',
            'at_type' => 'At Type',
            'deleted' => 'Deleted',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
