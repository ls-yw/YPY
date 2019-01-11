<?php
namespace app\basic;

class BasicLogic {
    public $errors = NULL;
    
    protected function _addError($error)
    {
        $this->errors[] = $error;
    }
    
    public function hasErrer()
    {
        return $this->errors ? true : false;
    }
    
}