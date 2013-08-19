<?php
/*
 * KEY RING
 *
 * This manager maintains a tree of credentials, used in accessing to codebook
 * database, and public-private key database, etc.
 * All decrypted credentials are stored in memory with expiry time. When time
 * excesses, the user is required to re-enter the main protection password.
 * 
 */
class KEYRING{

    private $keyring = array();
    private $disabled = false;
    
    public function __construct(){}

    public function get($key_name){
        global $__SESSION_MANAGER;
        if(!$this->_reload_keys()) return false;
        if(array_key_exists($key_name, $this->keyring)){
            if(null !== $__SESSION_MANAGER->token)
                return $__SESSION_MANAGER->token->decrypt(
                    $this->keyring[$key_name]
                );
        }
        return false;
    }

    public function set($key_name, $value){
        global $__SESSION_MANAGER;
        if(!$this->_reload_keys()) return false;

        if(null !== $__SESSION_MANAGER->token)
            $this->keyring[$key_name] = $__SESSION_MANAGER->token->encrypt(
                $value
            );
        else
            return false;

        return $this->_rewrite_keys();
    }

    private function _reload_keys(){
        global $__DATABASE;
        if($this->disabled) return false;
        if(false !== $user_id = $this->_get_user_id()){
            $query = $__DATABASE->select(
                'accounts',
                'id="' . $user_id . '"',
                'credentials_data,credentials_key_checksum'
            );
            if(!$row = $query->row()) return false;

            $fresh_checksum = $this->_get_main_key_checksum();
            if($fresh_checksum != $row['credentials_key_checksum'])
                return !($this->disabled = true);

            $this->keyring = null;
            $this->keyring = json_decode(
                $row['credentials_data'],
                true
            );

            return true;
        }
        return false;
    }

    private function _rewrite_keys(){
        global $__DATABASE;
        if($this->disabled) return false;
        $serialized = json_encode($this->keyring);
        if(false !== $user_id = $this->_get_user_id() && $serialized){
            $query = $__DATABASE->update(
                'accounts',
                array(
                    'credentials_data'=>$serialized,
                ),
                'id="' . $user_id . '"'
            );
            return true;
        }
        return false;
    }

    private function _get_user_id(){
        global $__SESSION_MANAGER;
        if($__SESSION_MANAGER->token !== null)
            return $__SESSION_MANAGER->token->get_user_id();
        return false;
    }

    private function _get_main_key_checksum(){
        global $__SESSION_MANAGER;
        if($__SESSION_MANAGER->token !== null)
            return $__SESSION_MANAGER->token->get_main_encrypt_key_hash();
        return false;
    }
}
