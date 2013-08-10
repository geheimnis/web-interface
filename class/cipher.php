<?php

class PASSPHRASE_PROVER{

    private $sign_key = null;

    public function __construct($sign_key){
        $this->sign_key = $sign_key;
    }

    public function generate($passphrase){
        $random = '';
        for($i=0;$i<32;$i++)
            $random .= chr(rand(0,255));

        $plaintext = hash_hmac('whirlpool', $random, $this->sign_key, false);

        $encryptor = new CIPHER($passphrase);
        $ciphertext = $encryptor->encrypt($plaintext);

        return base64_encode($random) . '$' . $ciphertext;
    }

    public function validate($test_passphrase, $evidence){
        try{
            $parts = explode('$', $evidence);
            $random = base64_decode($parts[0]);
            $ciphertext = $parts[1];

            $decryptor = new CIPHER($test_passphrase);
            if(false !== $plaintext = $decryptor->decrypt($ciphertext)){
                $test_plaintext = hash_hmac(
                    'whirlpool',
                    $random,
                    $this->sign_key,
                    false
                );
                if($plaintext == $test_plaintext) return true;
            }

        } catch (Exception $e){
        }
        return false;
    }

}

/*
 * CIPHER
 *
 * A symmetric cipher implemention using PHP's mcrypt.
 * This cipher shall fast, but not suitable for interoperating with non-PHP
 * programs.
 */
class CIPHER{

    private $ALGORITHM = MCRYPT_RIJNDAEL_256;
    private $OPERATE_MODE = MCRYPT_MODE_CBC;

    private $key = null;
    
    public function __construct($key){
        $this->key = substr(
            hash('whirlpool', $key, true),
            0,
            mcrypt_get_key_size($this->ALGORITHM, $this->OPERATE_MODE)
        );
    
    }

    public function encrypt($plaintext){
        $iv_size = mcrypt_get_iv_size(
            $this->ALGORITHM,
            $this->OPERATE_MODE
        );
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $block_size = mcrypt_get_block_size(
            $this->ALGORITHM,
            $this->OPERATE_MODE
        );
    
        $plaintext_utf8 = utf8_encode($plaintext);
        $checksum = hash('md5', $plaintext_utf8, true);
        $padding_length = $block_size - strlen($plaintext_utf8) % $block_size;
        if($padding_length == $block_size) $padding_length = 0;
        $plaintext_utf8 .= str_repeat('*', $padding_length);
    
        $ciphertext = mcrypt_encrypt(
            $this->ALGORITHM,
            $this->key,
            $plaintext_utf8,
            $this->OPERATE_MODE,
            $iv
        );

        $ciphertext = $iv . chr($padding_length) . $checksum . $ciphertext;
        
        $ciphertext_base64 = base64_encode($ciphertext);

        return $ciphertext_base64;

    }

    public function decrypt($ciphertext){
        $ciphertext_base64 = $ciphertext;
        $ciphertext_dec = base64_decode($ciphertext_base64);
        $iv_size = mcrypt_get_iv_size(
            $this->ALGORITHM,
            $this->OPERATE_MODE
        );
        $block_size = mcrypt_get_block_size(
            $this->ALGORITHM,
            $this->OPERATE_MODE
        );
        
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);
        $padding_length = ord(substr($ciphertext_dec, $iv_size, 1));
        $checksum = substr($ciphertext_dec, $iv_size+1, 16);
        $ciphertext_dec = substr($ciphertext_dec, $iv_size+17);

        $plaintext_utf8_dec = mcrypt_decrypt(
            $this->ALGORITHM,
            $this->key,
            $ciphertext_dec,
            $this->OPERATE_MODE,
            $iv_dec
        );

        $plaintext = substr($plaintext_utf8_dec, 0, -$padding_length);
        $checksum_got = hash('md5', $plaintext, true);

        if($checksum_got == $checksum)
            return $plaintext;
        else
            return false;
    }

}

class XIPHER{
}
