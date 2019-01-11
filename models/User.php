<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $realname 真实姓名
 * @property string $mobile 手机号码
 * @property string $password 密码
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
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
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['realname', 'mobile', 'password'], 'required'],
            [['realname'], 'string', 'max' => 10, 'min'=>2],
            [['mobile'], 'string', 'max' => 11, 'min'=>11],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'realname' => '真实姓名',
            'mobile' => '手机号码',
            'password' => '密码',
            'deleted' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if(!$token)return null;
        $user = \Yii::$app->redis->get($token);
        if(!$user)return null;
        $user = json_decode($user, true);
        return new static($user);
    }
    
    public static function findIdentity($id){}
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAuthKey(){}
    
    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey){}
}
