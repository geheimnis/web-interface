<?php
/*
 * SECURE KEYS MANAGER
 *
 * This manager maintains a tree of credentials, used in accessing to codebook
 * database, and public-private key database, etc.
 * All decrypted credentials are stored in memory with expiry time. When time
 * excesses, the user is required to re-enter the main protection password.
 * 
 */
class SECURE_KEYS_MANAGER{
    
    public function __construct(){
        
    }

    public function discard_all_credentials(){
        /*
         * Erase all stored decrypted credentials
         *
         * This is necessary, to discard all stored credentials as quickly as
         * possible.
         */
    }

}
