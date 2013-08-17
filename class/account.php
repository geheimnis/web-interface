<?php
class ACCOUNT{

    private $loaded = false;
    
    public function __construct($userid=null){
        global $_CONFIGS;


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

    }

    public function create($username, $password){
    }


    private function _passphrase_store($passphrase){
        global $_CONFIGS;
        $random = '';
        for($i=0;$i<32;$i++)
            $random .= chr(rand(0,255));

        $plaintext = hash_hmac(
            'whirlpool',
            $random,
            $_CONFIGS['security']['session']['sign_key'],
            false
        );

        $encryptor = new CIPHER($passphrase);
        $ciphertext = $encryptor->encrypt($plaintext);

        return base64_encode($random) . '$' . $ciphertext;
    }

    private function _passphrase_validate($test_passphrase, $evidence){
        global $_CONFIGS;
        try{
            $parts = explode('$', $evidence);
            $random = base64_decode($parts[0]);
            $ciphertext = $parts[1];

            $decryptor = new CIPHER($test_passphrase);
            if(false !== $plaintext = $decryptor->decrypt($ciphertext)){
                $test_plaintext = hash_hmac(
                    'whirlpool',
                    $random,
                    $_CONFIGS['security']['session']['sign_key'],
                    false
                );
                if($plaintext == $test_plaintext) return true;
            }

        } catch (Exception $e){
        }
        return false;
    }
}
