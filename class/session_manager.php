<?php
class TOKEN{
    
    public function __construct($token_string=null){
        if($token_string)
            $this->_load_token($token_string);
    }

    public function generate($username, $userid, $ua_pattern){
        global $_CONFIGS;

        $random = '';
        for($i=0;$i<96;$i++) $random .= chr(rand(0,255));

        $encrypt_key_server = sha1(substr($random,0,32));
        $encrypt_key_client = sha1(substr($random,32,32));
        $discard_key = sha1(substr($random, 64, 32));

        $record = $__DATABASE->insert(
            'sessions',
            array(
                'userid'=>$userid,
                'data'=>'',
                'encrypt_key_server'=>$encrypt_key_server,
                'encrypt_key_client_checksum'=>
                    hash_hmac(
                        'sha1',
                        $encrypt_key_client,
                        $_CONFIGS['security']['session']['sign_key']
                    ),
                'discard_key_checksum'=>md5($discard_key),
                'expire'=>
                    time() + $_CONFIGS['security']['session']['life'],
            )
        );

        $record_id = $record->insert_id();
        $client_key_encryptor = new CIPHER($ua_pattern);
        $client_key_encrypted = $client_key_encryptor->encrypt(
            $encrypt_key_client
        );
        

        $__IO
            ->cookie(
                'token',
                $record_id . '*' . $client_key_encrypted)
            ->cookie('discard_token', $discard_key)
        ;
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
    
    public $token = null;

    public function __construct(){
        global $__IO;

    }

    public function login($username, $password){
        global $__DATABASE, $__IO, $_CONFIGS;

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
            $this->token = new TOKEN();
            $this->token->generate(
                base64_encode(trim($username)),
                $row['id'],
                $this->_user_agent_pattern(),
            );
            return true;
        } else
            return false;
    }

    private function _auth_by_cookie(){
        global $__IO;
        $token = 
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
