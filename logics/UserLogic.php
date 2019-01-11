<?php
namespace app\logics;

use app\basic\BasicLogic;
use app\models\User;
use app\models\UserRelation;

class UserLogic extends BasicLogic{
    
    public function register($data) {
        $userModel = (new User());
        
        if($userModel->load($data) && $userModel->save()){
            return true;
        }
        foreach ($userModel->errors as $val){
            $this->_addError(is_array($val) ? $val[0] : $val);
        }
        return false;
    }
    
    public function getByMobile($mobile)
    {
        $userModel = (new User());
        return $userModel->find()->where(['mobile'=>$mobile, 'deleted'=>0])->asArray()->one();
    }
    
    public function getUserRelation($uid)
    {
        $cwdr = $this->getFinancer($uid);
        $baoxiao = $this->getExpenser($uid);
        return ['cwdr'=>$cwdr, 'baoxiao'=>$baoxiao];
    }
    
    public function getUserRelationPairs($uid)
    {
        $userRelation = $this->getUserRelation($uid);
        $users = [];
        if(!empty($userRelation['cwdr'])){
            foreach ($userRelation['cwdr'] as $val){
                $users[$val->to_uid] = $val->to_realname;
            }
        }
        if(!empty($userRelation['baoxiao'])){
            foreach ($userRelation['baoxiao'] as $val){
                $users[$val->uid] = $val->realname;
            }
        }
        return $users;
    }
    
    public function getFinancer($uid)
    {
        $financer = UserRelation::getFinancer($uid);
        if($financer){
            foreach ($financer as &$val){
                $val->to_realname = $this->getUserRealNameById($val->to_uid);
            }
        }
        return $financer;
    }
    
    public function getExpenser($uid)
    {
        $expenser = UserRelation::getExpenser($uid);
        if($expenser){
            foreach ($expenser as &$val){
                $val->realname = $this->getUserRealNameById($val->uid);
            }
        }
        return $expenser;
    }
    
    public function addCWDR($data)
    {
        $userRelationModel = (new UserRelation());
        foreach ($data as $key => $val){
            $userRelationModel->{$key} = $val;
        }
        if($userRelationModel->save()){
            return true;
        }
        foreach ($userRelationModel->errors as $val){
            $this->_addError($val);
        }
        return false;
    }
    
    public function delUserRelation($id, $deleted=1)
    {
        $userRelationModel = UserRelation::findOne($id);
        $userRelationModel->deleted = $deleted;
        
        if($userRelationModel->save()){
            return true;
        }
        foreach ($userRelationModel->errors as $val){
            $this->_addError($val);
        }
        return false;
    }
    
    public function getUserById($uid)
    {
        return User::findOne(['id'=>$uid, 'deleted'=>0]);
    }
    
    public function getUserRealNameById($uid)
    {
        $user = $this->getUserById($uid);
        if(!$user)return '';
        return $user->realname;
    }
    
}