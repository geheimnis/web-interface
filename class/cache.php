<?php
class CACHE{

    private $max_life = 0;
    
    public function __construct(){
        global $_CONFIGS;
        $this->max_life = $_CONFIGS['performance']['cache']['max_life'];
    }

    public function item($key, $value=null){
        global $__DATABASE;
        
        $query_key = sha1($key);
        $result = $__DATABASE->select(
            'cache',
            'name="' . $query_key . '"',
            'value, updated_time'
        );
        $row = $result->row();

        if($value === null){
            if(!$row) return false;
            if($row['updated_time'] < time() - $this->max_life)
                return base64_decode($row['value']);
        } else {
            $create = false;
            if(!$row) $create = true;
            if($create)
                $__DATABASE->insert(
                    'cache',
                    array(
                        'name'=>$query_key,
                        'value'=>base64_encode($value),
                        'updated_time'=>time(),
                    )
                );
            else 
                $__DATABASE->update(
                    'cache',
                    array(
                        'value'=>base64_encode($value),
                        'updated_time'=>time(),
                    ),
                    'name="' . $query_key . '"'
                );
        }
    }

    public function clean(){
        global $__DATABASE;
        $__DATABASE->delete(
            'cache',
            'updated_time < ' . time() - $this->max_life
        );
    }

    public function purge(){
        global $__DATABASE;
        $__DATABASE->delete('cache');
    }
}

$__CACHE = new CACHE();
