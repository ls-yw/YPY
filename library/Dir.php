<?php
namespace app\library;

class Dir {
    /**
     * 自动创建目录
     * @param string $dir
     * @return boolean
     * @create_time 2017年11月21日
     */
    public static function directory( $dir ){  
       return  is_dir ( $dir ) or self::directory(dirname( $dir )) and  mkdir ( $dir , 0777);
    }
}