<?php
class CACHE{

    private $max_life = 0;
    
    public function __construct(){
        global $_CONFIGS;
        $this->max_life = $_CONFIGS['performance']['cache']['max_life'];
    }

    public function item($key, $value=null){
        global $__DATABASE, $__SESSION_MANAGER;

        if($__SESSION_MANAGER->token)
            $user_id = $__SESSION_MANAGER->token->get_user_id();
        else
            return false;

        $query_key = sha1($key);
        $result = $__DATABASE->select(
            'cache',
            'name="' . $query_key . '" AND user_id="' . $user_id . '"',
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
                        'user_id'=>$user_id,
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
                    'name="' . $query_key . '" AND user_id="' . $user_id . '"'
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
