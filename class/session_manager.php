<?php
class SESSION_MANAGER{

    public function __construct(){
        global $__IO;

    }

    private function _auth_by_cookie(){
        global $__IO;

    }

    private function _user_agent_pattern(){
        global $_SERVER, $_CONFIGS;
        $wanted = array(
            'REMOTE_ADDR',
            'HTTP_USER_AGENT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_DNT',
            'HTTP_CACHE_CONTROL',
        );
        $result = array();
        foreach($wanted as $key)
            $result[$key] =
                array_key_exists($key, $_SERVER)?$_SERVER[$key]:'';
    
        ksort($result);
        $to_be_hashed = '';
        foreach($result as $key=>$value)
            $to_be_hashed .= ':' . $value;

        return hash(
            $_CONFIGS['security']['session']['id_hash_algorithm'],
            $to_be_hashed,
            false
        );
    }

}

$__SESSION_MANAGER = new SESSION_MANAGER();
