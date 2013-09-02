<?php
class CACHE{

    private $max_life = 0;
    
    public function __construct(){
        global $_CONFIGS;
        $this->max_life = $_CONFIGS['performance']['cache']['max_life'];
    }

    public function item($key, $value=null){
        global $__DATABASE;
        if($value === null){
        } else {
        }
    }

    public function clean(){
    }

    public function purge(){
    }
}
