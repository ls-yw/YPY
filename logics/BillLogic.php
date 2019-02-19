<?php
namespace app\logics;

use app\basic\BasicLogic;
use app\models\Expense;
use app\models\ExpenseImg;

class BillLogic extends BasicLogic {
    
    public function saveExpense($data)
    {
        $expenseModel = isset($data['Expense']['id']) && !empty($data['Expense']['id']) ? Expense::findOne($data['Expense']['id']) : new Expense();
        if($expenseModel->load($data) && $expenseModel->save()){
            return $expenseModel;
        }
        foreach ($expenseModel->errors as $val){
            $this->_addError(is_array($val) ? $val[0] : $val);
        }
        return false;
    }
    
    public function getExpenseImgs($expenseId)
    {
        $data = ['exp'=>null];
        $imgs = ExpenseImg::findAll(['expense_id'=>$expenseId, 'deleted'=>0]);
        if($imgs){
            foreach ($imgs as $val){
                if($val->at_type == 'exp')$data['exp'][] = $val;
            }
        }
        return $data;
    }
    
    public function saveExpenseImgs($imgData, $expenseId)
    {
        $imgs = $this->getExpenseImgs($expenseId);
        
        //统一先全部删除
        $this->delExpenseImgs($expenseId, 'exp');
        
        if(empty($imgData))return true;
        
        foreach ($imgData as $val) {
            $exist = false;
            $imgId = 0;
            if(!empty($imgs['exp'])){
                foreach ($imgs['exp'] as $v) {
                    if($v->img_url ==$val['img_url']){
                        $exist = true;
                        $imgId = $v->id;
                    }
                }
            }
            if($exist){
                $this->unDelExpenseImgs($imgId);
            }else{
                $expenseImgModel = new ExpenseImg();
                foreach ($val as $kk => $vv){
                    $expenseImgModel->{$kk} = $vv;
                }
                $expenseImgModel->save();
            }
        }
    }
    
    public function delExpenseImgs($expenseId, $type)
    {
        return ExpenseImg::updateAll(['deleted'=>1], ['expense_id'=>$expenseId, 'at_type'=>$type]);
    }
    
    public function unDelExpenseImgs($id)
    {
        return ExpenseImg::updateAll(['deleted'=>0], ['id'=>$id]);
    }
    
    public function getExpenseById($id)
    {
        return Expense::findOne($id);
    }
    
    public function getExpense(int $uid, int $toUid, string $date=NULL, array $atStatus, $uidToUid='and', $offset=NULL, $limit=NULL, $orderBy='update_time desc')
    {
        $expenseModel = Expense::find();
        $expenseModel->with('expenseImg');
        $expenseModel->andWhere(['at_status'=>$atStatus, 'deleted'=>0]);
        if($date)$expenseModel->andWhere(['at_date'=>$date]);
        if($uidToUid == 'or' && !empty($uid) && !empty($toUid)){
            $expenseModel->andWhere(['or', "uid={$uid}", "to_uid={$toUid}"]);
        }else{
            if(!empty($uid))$expenseModel->andWhere(['uid'=>$uid]);
            if(!empty($toUid))$expenseModel->andWhere(['to_uid'=>$toUid]);
        }
        
        if($offset !== null && $limit != null){
            $expenseModel->offset($offset)->limit($limit);
        }
        
        $expenseModel->orderBy($orderBy);
        $expenseModel->asArray();
        return $expenseModel->all();
    }
    
    public function getExpenseCount(int $uid, int $toUid, string $date=NULL, array $atStatus, $uidToUid='and')
    {
        $expenseModel = Expense::find();
        $expenseModel->andWhere(['at_status'=>$atStatus, 'deleted'=>0]);
        if($date)$expenseModel->andWhere(['at_date'=>$date]);
        if($uidToUid == 'or' && !empty($uid) && !empty($toUid)){
            $expenseModel->andWhere(['or', "uid={$uid}", "to_uid={$toUid}"]);
        }else{
            if(!empty($uid))$expenseModel->andWhere(['uid'=>$uid]);
            if(!empty($toUid))$expenseModel->andWhere(['to_uid'=>$toUid]);
        }
        
        return $expenseModel->count();
    }
    
    public function changeExpenseStatus($uid, $id, $status)
    {
        $expense = Expense::findOne($id);
        if($uid != $expense->uid && $uid != $expense->to_uid){
            $this->_addError('无权限更改');
            return false;
        }
        
        //是否有权限更改该状态
        $r = $this->checkPowerChangeExpense($uid, $expense, $status);
        if(!$r){
            $this->_addError('无权限更改');
            return false;
        }
        
        $expense->at_status = $status;
        if($expense->save()){
            return true;
        }
        foreach ($expense->errors as $val){
            $this->_addError('更改失败');
        }
        return false;
    }
        
    public function checkPowerChangeExpense($uid, $expense, $status)
    {
        if($uid == $expense->uid){  //报销者
            
            if($expense->at_type == 'income'){
                if($expense->at_status == 2 && in_array($status, [3,5]))return true;
                if($expense->at_status == 3 && in_array($status, [5]))return true;
            }else{
                if($expense->at_status == 1 && in_array($status, [5]))return true;
                if($expense->at_status == 2 && in_array($status, [5]))return true;
                if($expense->at_status == 3 && in_array($status, [4, 2]))return true;
            }
        }else{  //财务大人
            if($expense->at_type == 'income'){
                if($expense->at_status == 3 && in_array($status, [4, 2]))return true;
            }else{
                if($expense->at_status == 1 && in_array($status, [2,6]))return true;
                if($expense->at_status == 2 && in_array($status, [3,6]))return true;
            }
        }
        return false;
    }
    
}