<?php
namespace app\controllers;

use app\basic\BasicController;
use app\logics\UserLogic;
use yii\helpers\Url;

class LoginController extends BasicController {
    
    function actionIndex() {
        if(\Yii::$app->request->isAjax){
            $mobile   = $this->postParam('mobile', 'trim');
            $password = $this->postParam('password', 'trim');
            $remember = $this->postParam('remember', 'trim');
            
            if(empty($mobile) || strlen($mobile) < 11){
                return $this->ajaxReturn(2, '手机号码错误');
            }
            
            if(empty($password) || strlen($password) < 6){
                return $this->ajaxReturn(2, '密码错误');
            }
            
            $u = (new UserLogic())->getByMobile($mobile);
            if(!$u || $u['password'] != md5($password)){
                return $this->ajaxReturn(1, '手机号码或密码错误');
            }
            $token = md5($mobile);
            $expire = 3600;
            if($remember)$expire = 86400 * 30;
            \Yii::$app->redis->setex($token, $expire, json_encode($u));
            
            //设置加密token的cookie值
            $tokenCookie = $this->encryptCookie('token', $token);
            
            return $this->ajaxReturn(0, '登录成功', ['token'=>$token, 'cookie'=>['name'=>'token', 'value'=>$tokenCookie], 'url'=>Url::to(['bill/index'])]);
        }
        
        if(!\Yii::$app->user->isGuest){
            return $this->redirect(['bill/index']);
        }
        
        $this->layout = false;
        return $this->render('login');
    }
    
    public function actionRegister()
    {
        if(\Yii::$app->request->isAjax){
            $mobile   = $this->postParam('mobile', 'trim');
            $password = $this->postParam('password', 'trim');
            $realname = $this->postParam('realname', 'trim');
            
            if(empty($password) || strlen($password) < 6){
                return $this->ajaxReturn(2, '密码错误');
            }
            
            $u = (new UserLogic())->getByMobile($mobile);
            if($u){
                return $this->ajaxReturn(2, '该手机号码已注册');
            }
            
            $data = [];
            $data['User']['mobile'] = $mobile;
            $data['User']['password'] = md5($password);
            $data['User']['realname'] = $realname;
            
            $userLogic = new UserLogic();
            $userLogic->register($data);
            if(!$userLogic->hasErrer()){
                return $this->ajaxReturn(0, '注册成功', Url::to(['login/index']));
            }
            return $this->ajaxReturn(2, implode('，', $userLogic->errors));
        }
        
        $this->layout = false;
        return $this->render('register');
    }
    
    public function actionLogout()
    {
        if($this->token){
            \Yii::$app->redis->del($this->token);
            \Yii::$app->response->cookies->remove('token');
        }
        return $this->redirect(['login/index']);
    }
    
}