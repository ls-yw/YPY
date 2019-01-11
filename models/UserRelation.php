<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_relation}}".
 *
 * @property int $id
 * @property int $uid 报销人ID
 * @property int $to_uid 财务大人ID
 * @property int $deleted 状态 0正常 1删除 2审核中 3拒绝
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class UserRelation extends \yii\db\ActiveRecord
{
    
    public $to_realname;
    public $realname;
    
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
        return '{{%user_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'to_uid', 'deleted'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '报销人ID',
            'to_uid' => '财务大人ID',
            'deleted' => '状态 0正常 1删除 2审核中 3拒绝',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    public static function getFinancer($uid)
    {
        return self::findAll(['uid'=>$uid, 'deleted'=>[0, 2, 3]]);
    }
    
    public static function getExpenser($uid)
    {
        return self::findAll(['to_uid'=>$uid, 'deleted'=>[0, 2]]);
    }
}
