<?php
namespace logic;

use Basic\BasicLogic;
use models\Config;

class ConfigLogic extends BasicLogic
{
    /**
     * 获取配置
     *
     * @author yls
     * @param string|null $type
     * @param string|null $name
     * @return array
     * @throws \Exception
     */
    public function getConfig(string $type = null, string $name = null)
    {
        $where = [];
        if(null !== $type) {
            $where['config_type'] = $type;
        }
        if(null !== $name) {
            $where['config_name'] = $name;
        }
        $res = (new Config())->getList($where);
        if (null !== $type && null !== $name && 1 === count($res)) {
            return $res[0];
        }
        return $res;
    }
}