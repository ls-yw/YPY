<?php

namespace app\models;

use Yii;

class Config extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['config_name'], 'string', 'max'=>20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'config_type' => 'config_type',
            'config_name' => 'config_name',
            'config_value' => 'config_value',
        ];
    }
}
