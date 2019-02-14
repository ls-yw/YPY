<?php
namespace app\basic;

use yii\web\Controller;
use app\models\User;
use app\logics\ConfigLogic;

class BasicController extends Controller
{
    
    protected $token;
    protected $_uid = null;
    protected $sytemConfig = [];
    
    public function init()
    {
        parent::init();
        $this->token = \Yii::$app->request->cookies->has('token') ? \Yii::$app->request->cookies->get('token') : $this->decryptCookie('token', $this->getParam('token'));
        \Yii::$app->user->switchIdentity(User::findIdentityByAccessToken($this->token), 0);
        $this->_uid = !\Yii::$app->user->isGuest ? \Yii::$app->user->identity->id : null;
        $this->sytemConfig = (new ConfigLogic())->getConfig('system');
    }
    
    public function ajaxReturn($code, $msg, $data = NULL, $other=NULL)
    {
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = ['code'=>$code, 'msg'=>$msg];
        if($data)$response->data['data'] = $data;
        if($other && is_array($other)){
            foreach ($other as $key => $val){
                $response->data[$key] = $val;
            }
        }
    }
    
    protected function getParam(string $key=NULL, string $filters=NULL, string $default=NULL)
    {
        $value = (!$key) ? \Yii::$app->request->get() : \Yii::$app->request->get($key, $default);
        return $this->_filterParams($value, $filters);
    }
    
    protected function postParam(string $key=NULL, string $filters=NULL, string $default=NULL)
    {
        $value = (!$key) ? \Yii::$app->request->post() : \Yii::$app->request->post($key, $default);
        return $this->_filterParams($value, $filters);
    }
    
    private function _filterParams($value, string $filters = NULL)
    {
        if(!empty($filters)){
            $filters = explode('|', $filters);
            if (is_array($value)){
                foreach ($value as &$val){
                    foreach ($filters as $v){
                        $val = $this->_doFilterParam($val, $v);
                    }
                }
            }else{
                foreach ($filters as $v){
                    $value = $this->_doFilterParam($value, $v);
                }
            }
        }
        return $value;
    }
    
    private function _doFilterParam(string $value, string $filters)
    {
        $value = trim($value);
        switch ($filters){
            case 'intval':
                $value = intval($value);
                break;
            case 'string':
                $value = strip_tags($value);
                break;
            case 'int':
                $value = (int)preg_replace('/(\D)/i', '', $value);
                break;
        }
        return $value;
    }
    
    protected function decryptCookie($name, $value)
    {
        if(!$value)return null;
        $validationKey = \Yii::$app->request->cookieValidationKey;
        $data = \Yii::$app->getSecurity()->validateData($value, $validationKey);
        $data = @unserialize($data);
        if (is_array($data) && isset($data[0], $data[1]) && $data[0] === $name) {
            return $data[1];
        }
        return null;
    }
    
    protected function encryptCookie($name, $value)
    {
        $validationKey = \Yii::$app->request->cookieValidationKey;
        return \Yii::$app->getSecurity()->hashData(serialize([$name, $value]), $validationKey);
    }
    
}