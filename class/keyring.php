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
    
    public function __construct(){
        
    }

    public function get($key_name){
        global $__DATABASE;
        if(false !== $user_id = $this->_get_user_id()){
            
        }
    }

    public function set($key_name){
    }

    public function discard_all_credentials(){
        /*
         * Erase all stored decrypted credentials
         *
         * This is necessary, to discard all stored credentials as quickly as
         * possible.
         */
    }

    private function _get_user_id(){
        global $__SESSION_MANAGER;
        if($__SESSION_MANAGER->token !== null)
            return $__SESSION_MANAGER->token->get_user_id();
        return false;
    }

}
