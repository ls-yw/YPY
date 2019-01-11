<?php
namespace app\controllers;

use app\basic\BasicController;
use yii\filters\AccessControl;
use app\logics\UserLogic;

class UserController extends BasicController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        (new UserLogic())->getUserRelation($this->_uid);
        
        return $this->render('index');
    }
    
    public function actionAddrelation()
    {
        if(\Yii::$app->request->isAjax){
            $mobile = $this->postParam('mobile');
            if(!$mobile && $mobile == \Yii::$app->user->identity->mobile){
                return $this->ajaxReturn(1, '请输入正确的手机号码');
            }
            
            $userLogic = (new UserLogic());
            
            $tUser = (new UserLogic())->getByMobile($mobile);
            if(empty($tUser)){
                return $this->ajaxReturn(1, '该用户不存在');
            }
            if($tUser['id'] == \Yii::$app->user->identity->id){
                return $this->ajaxReturn(1, '请输入正确的手机号码');
            }
            
            $cwdrs = $userLogic->getFinancer($this->_uid);
            if($cwdrs){
                foreach ($cwdrs as $val) {
                    if($val->id == $tUser['id'])return $this->ajaxReturn(1, '你已添加该财务大人');
                }
            }
            
            $data = [];
            $data['uid']     = \Yii::$app->user->identity->id;
            $data['to_uid']  = $tUser['id'];
            $data['deleted'] = 2;
            
            $userLogic->addCWDR($data);
            
            if(!$userLogic->hasErrer()){
                return $this->ajaxReturn(0, '添加成功，请等待财务大人的审核', ['realName'=>$tUser['realname'], 'toUid'=>$tUser['id']]);
            }
            return $this->ajaxReturn(2, implode('，', $userLogic->errors));
        }
    }
    
    public function actionCancelrelation()
    {
        if(\Yii::$app->request->isAjax){
            $toUid = $this->postParam('touid', 'int', 0);
            $userLogic = (new UserLogic());
            $userRelation = $userLogic->getFinancer($this->_uid);
            if($userRelation){
                foreach ($userRelation as $val){
                    if($val->deleted == 2 && $val->to_uid == $toUid){
                        $userLogic->delUserRelation($val->id);
                        if(!$userLogic->hasErrer()){
                            return $this->ajaxReturn(0, '取消成功');
                        }
                        return $this->ajaxReturn(2, implode('，', $userLogic->errors));
                    }
                }
            }
            return $this->ajaxReturn(1, '无可以取消的申请');
        }
    }
    
    public function actionExrelation()
    {
        if(\Yii::$app->request->isAjax){
            $id = $this->postParam('id', 'intval', 0);
            $t  = $this->postParam('t', 'string', '');
            $userLogic = (new UserLogic());
            $userRelation = $userLogic->getExpenser($this->_uid);
            
            if($userRelation){
                foreach ($userRelation as $val){
                    if($val->id == $id && $val->deleted == 2 && in_array($t, ['ok', 'no'])){
                        $userLogic->delUserRelation($val->id, ($t == 'ok' ? 0 : 3));
                        if(!$userLogic->hasErrer()){
                            return $this->ajaxReturn(0, ($t == 'ok' ? '同意' : '拒绝').'成功');
                        }
                        return $this->ajaxReturn(2, implode('，', $userLogic->errors));
                    }
                }
            }
            return $this->ajaxReturn(1, '操作失败');
        }
    }
}