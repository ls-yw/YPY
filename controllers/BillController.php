<?php
namespace app\controllers;

use app\basic\BasicController;
use yii\filters\AccessControl;
use app\logics\UserLogic;
use app\logics\BillLogic;

class BillController extends BasicController
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
        $uid = \Yii::$app->user->identity->id;
        $relation = (new UserLogic())->getUserRelation($uid);
        $blockquote = '';
        if(!$relation['cwdr'] && !$relation['baoxiao']){
            $blockquote = '你还没有财务大人，现在<a href="javascript:;" class="red addCwdr">添一个</a>？';
        }elseif($relation['cwdr'] && !$relation['baoxiao']){ //报销者的身份
            $time = 0;
            foreach ($relation['cwdr'] as $val) {
                if($val->deleted == 2 && $time < strtotime($val->update_time)){
                    $blockquote = '等待财务大人('.(new UserLogic())->getUserRealNameById($val->to_uid).')的审核，<a href="javascript:;" class="red cancelCwdr" data-touid="'.$val->to_uid.'">取消申请</a>？';
                    $time = strtotime($val['update_time']);
                }elseif ($val->deleted == 3 && $time < strtotime($val->update_time)){
                    $blockquote = (new UserLogic())->getUserRealNameById($val->to_uid).'已拒绝当你的财务大人。';
                    $time = strtotime($val['update_time']);
                }
            }
        }elseif(!$relation['cwdr'] && $relation['baoxiao']){ //财务大人的身份
            foreach ($relation['baoxiao'] as $val) {
                if($val->deleted == 2){
                    $blockquote = (new UserLogic())->getUserRealNameById($val->uid).' 申请成为你的报销者。'.
                        '<a href="javascript:;" class="layui-btn layui-btn-xs layui-btn-radius okBao" data-id="'.$val->id.'">同意</a> <a href="javascript:;" class="layui-btn layui-btn-xs layui-btn-radius layui-btn-danger noBao" data-id="'.$val->id.'">拒绝</a>';
                    $time = strtotime($val['update_time']);
                }
            }
        }
        
        return $this->render('index', ['blockquote'=>$blockquote, 'user'=>\Yii::$app->user->identity]);
    }
    
    public function actionExpense()
    {
        if(\Yii::$app->request->isAjax){
            $page = $this->getParam('page', 'intval', 1);
            
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            $billLogic = new BillLogic();
            $list = $billLogic->getExpense($this->_uid, $this->_uid, null, [1,2,3,4,5,6], 'or', $offset, $limit, 'at_status asc,update_time desc');
            $total = $billLogic->getExpenseCount($this->_uid, $this->_uid, null, [1,2,3,4,5,6], 'or');
            
            $users = (new UserLogic())->getUserRelationPairs($this->_uid);
            if($list){
                $users[$this->_uid] = \Yii::$app->user->identity->realname;
                foreach ($list as &$val){
                    $val['realname'] = $users[$val['uid']] ?? '';
                    $val['to_realname'] = $users[$val['to_uid']] ?? '';
                    $val['role'] = ($val['uid'] == $this->_uid) ? 'expenser' : 'financer';
                }
            }
            
            return $this->ajaxReturn(0, 'ok', $list, ['pageInfo'=>['page'=>$page, 'total'=>$total, 'pages'=>ceil($total/$limit)]]);
        }
    }
    
    public function actionAddexpense()
    {
        $type = $this->getParam('type', 'string', '');
        $userLogic = new UserLogic();
        $financer = $userLogic->getFinancer($this->_uid);
        
        $assign = [];
        $assign['financer'] = $financer;
        $assign['type']     = $type;
        
        $viewName = $type == 'income' ? 'setincome' : 'setexpense';
        return $this->render($viewName, $assign);
    }
    
    public function actionEditexpense()
    {
        $userLogic = new UserLogic();
        $financer = $userLogic->getFinancer($this->_uid);
        
        $error = '';
        $id = $this->getParam('id', 'int', 0);
        if(empty($id))$error = '数据不存在';
        
        $billLogic = new BillLogic();
        $expense = $billLogic->getExpenseById($id);
        if(!$expense)$error = '数据不存在';
        $expenseImg = $billLogic->getExpenseImgs($expense->id);
        
        $type = $expense->at_type;
    
        $assign = [];
        $assign['financer']    = $financer;
        $assign['error']       = $error;
        $assign['expense']     = $expense;
        $assign['expenseImg']  = $expenseImg;
        $assign['type']        = $type;
        
        $viewName = $type == 'income' ? 'setincome' : 'setexpense';
        return $this->render($viewName, $assign);
    }
    
    public function actionSaveexpense()
    {
        if(\Yii::$app->request->isAjax){
            $id   = $this->postParam('id', 'int', 0);
            $imgs = $this->postParam('img');
            $data = ['Expense'=>[]];
            $data['Expense']['uid']     = $this->_uid;
            $data['Expense']['to_uid']  = $this->postParam('to_uid', 'int', 0);
            $data['Expense']['cate_id'] = $this->postParam('cate_id', 'int', 0);
            $data['Expense']['title']   = $this->postParam('title', 'string');
            $data['Expense']['content'] = $this->postParam('content', 'string');
            $data['Expense']['at_date'] = $this->postParam('at_date', 'string');
            $data['Expense']['price']   = $this->postParam('price');
            $data['Expense']['at_type'] = $this->postParam('type', 'string', 'expense');
            
            if(!empty($id))$data['Expense']['id'] = $id;
            if(empty($id) && $data['Expense']['at_type'] == 'income')$data['Expense']['at_status'] = 2;
            
            $billLogic = new BillLogic();
            $expense = $billLogic->saveExpense($data);
            if(!$billLogic->hasErrer()){
                //上传图片
                $imgData = [];
                if(!empty($imgs)){
                    foreach ($imgs as $key => $val) {
                        $imgData[$key]['expense_id'] = $expense->id;
                        $imgData[$key]['uid']        = $this->_uid;
                        $imgData[$key]['img_url']    = $val;
                        $imgData[$key]['at_type']    = 'exp';
                    }
                }
                $billLogic->saveExpenseImgs($imgData, $expense->id);
                
                
                return $this->ajaxReturn(0, '保存成功');
            }
            return $this->ajaxReturn(2, implode('，', $billLogic->errors));
        }
    }
    
    public function actionChangeexpense()
    {
        if(\Yii::$app->request->isAjax){
            $id     = $this->postParam('id', 'int', 0);
            $status = $this->postParam('status', 'intval', 0);
            if(empty($id) || empty($status))return $this->ajaxReturn(1, '参数错误');
            
            $billLogic = new BillLogic();
            $billLogic->changeExpenseStatus($this->_uid, $id, $status);
            
            if(!$billLogic->hasErrer()){
                return $this->ajaxReturn(0, 'ok');
            }
            return $this->ajaxReturn(2, implode('，', $billLogic->errors));
        }
    }
}