<?php
class TOKEN{
    
    public function __construct($token_string=null){
        if($token_string)
            $this->_load_token($token_string);
    }

    public function generate($username, $userid, $ua_pattern, $life){
        
    }

    public function discard($discard_key){
        global $__DATABASE;
        $__DATABASE->delete(
            'sessions',
            'discard_key_checksum="' . md5($discard_key) . '"'
        );
        $time = time();
        $__DATABASE->delete(
            'sessions',
            'expire < ' . $time
        );
        return $this;
    }

    private function _load_token($token_string){
    }

}

class SESSION_MANAGER{

    public function __construct(){
        global $__IO;

    }

    public function login($username, $password){
        global $__DATABASE;
        $result = $__DATABASE->select(
            'accounts',
            'username="' . md5(strtolower(trim($username))) . '"',
            'id,authproof'
        );
        $row = $result->row();

        $authresult = false;
        if($row){
            $authproof = $row['authproof'];
            $passprover = new PASSPHRASE_PROVER();
            $authresult = 
                (true === $passprover->validate($password, $authproof));
        }

        if(true === $authresult){
            # issue Token
            
        } else
            return false;
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
