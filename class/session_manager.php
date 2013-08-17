<?php
class TOKEN{

    public $session_manager = null;
    
    private $loaded = false;

    private $user_id = false;
    private $encrypt_key = false;
    private $token_id = false;
    
    public function __construct($session_manager){
        global $__IO;
        $this->session_manager = $session_manager;
        if($__IO->cookie('token'))
            $this->_load_token(
                $__IO->cookie('token'),
                $this->session_manager
            );
    }

    public function issue($account_instance){
        global $_CONFIGS, $__DATABASE, $__IO;

        $userid = $account_instance->get('id');
        $username = $account_instance->get('username');

        $random = '';
        for($i=0;$i<96;$i++) $random .= chr(rand(0,255));
        $ua_pattern = $this->session_manager->user_agent_pattern();

        print "UA-PATTERN: $ua_pattern ::";

        $encrypt_key_server = sha1(substr($random,0,32));
        $encrypt_key_client = sha1(substr($random,32,32));
        $discard_key = sha1(substr($random, 64, 32));

        $record = $__DATABASE->insert(
            'sessions',
            array(
                'userid'=>$userid,
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

        $this->user_id = $userid;
        $this->encrypt_key = $this->_derive_encrypt_key(
            $encrypt_key_server,
            $encrypt_key_client
        );
        $this->token_id = $record_id;

        $this->loaded = true;

        return $this->loaded;
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

    private function _load_token($token){
        global $__DATABASE, $_CONFIGS;

        $userid = $token_id = $encrypt_server_key = $encrypt_client_key =
            false;
        $loaded = false;

        try{
            $parsed_token = explode('*', $token);
            $token_id = $parsed_token[0];
            $client_key_encrypted = $parsed_token[1];

            if(is_numeric($token_id)){
                $client_key_decryptor = new CIPHER(
                    $this->session_manager->user_agent_pattern()
                );
                $encrypt_key_client =
                    $client_key_decryptor->decrypt($client_key_encrypted);

                if($encrypt_key_client){

                    $query_result = $__DATABASE->select(
                        'sessions',
                        'id="' . $token_id . '"'
                    );
                    if($row = $query_result->row()){
                        $userid = $row['userid'];
                        $encrypt_key_server = $row['encrypt_key_server'];
                        $encrypt_key_checksum =
                            $row['encrypt_key_client_checksum'];
                        $encrypt_key_checksum_test = hash_hmac(
                            'sha1',
                            $encrypt_key_client,
                            $_CONFIGS['security']['session']['sign_key']
                        );

                        $loaded = (
                            $encrypt_key_checksum ==
                            $encrypt_key_checksum_test
                        );

                    }
                }
            }
        } catch(Exception $e){
        }
        
        if($loaded === true){
            $this->user_id = $userid;
            $this->encrypt_key = $this->_derive_encrypt_key(
                $encrypt_key_server,
                $encrypt_key_client
            );
            $this->token_id = $token_id;
            $this->loaded = true;
            return true;
        } else
            return false;
    }

    private function _derive_encrypt_key($server_key, $client_key){
        return hash('whirlpool', $server_key . $client_key, true);
    }

    public function is_loaded(){
        return $this->loaded;
    }

    public function get_user_id(){
        return $this->user_id;
    }

    public function get_token_id(){
        return $this->token_id;
    }
}

class SESSION_MANAGER{
    
    public $token = null;

    public function __construct(){
        global $__IO;
        $this->token = new TOKEN($this);
    }

    public function login($username, $password){
        global $__DATABASE, $__IO, $_CONFIGS;

        $account = new ACCOUNT();
        $authresult = $account->login($username, $password);

        if(true === $authresult){
            return $this->token->issue($account);
        } else
            return false;
    }

    public function user_agent_pattern(){
        global $_SERVER, $_CONFIGS;
        $wanted = array(
            'REMOTE_ADDR',
            'HTTP_USER_AGENT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_DNT',
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
