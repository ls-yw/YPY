<?php
namespace app\logics;

use app\basic\BasicLogic;
use app\models\Config;

class ConfigLogic extends BasicLogic
{
    
    public function getConfig($type = NULL)
    {
        $where = $type ? ['config_type'=>$type] : '';
        $configs = Config::findAll($where);
        $configArr = [];
        if($configs){
            foreach ($configs as $val){
                $configArr[$val->config_name] = $val->config_value;
            }
        }
        return $configArr;
    }
    
}