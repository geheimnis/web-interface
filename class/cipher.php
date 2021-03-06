<?php
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

    public function __destruct(){
        unset($this->key);
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
    
        $checksum = hash('md5', $plaintext, true);
        $padding_length = $block_size - strlen($plaintext) % $block_size;
        if($padding_length == $block_size) $padding_length = 0;
        $plaintext .= str_repeat('*', $padding_length);
    
        $ciphertext = mcrypt_encrypt(
            $this->ALGORITHM,
            $this->key,
            $plaintext,
            $this->OPERATE_MODE,
            $iv
        );
        unset($plaintext);

        $ciphertext = $iv . chr($padding_length + 1) . $checksum . $ciphertext;
        
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
        $padding_length = ord(substr($ciphertext_dec, $iv_size, 1)) - 1;
        $checksum = substr($ciphertext_dec, $iv_size+1, 16);
        $ciphertext_dec = substr($ciphertext_dec, $iv_size+17);

        $plaintext = mcrypt_decrypt(
            $this->ALGORITHM,
            $this->key,
            $ciphertext_dec,
            $this->OPERATE_MODE,
            $iv_dec
        );

        if($padding_length > 0)
            $plaintext = substr($plaintext, 0, -$padding_length);
        else
            $plaintext = $plaintext;
        $checksum_got = hash('md5', $plaintext, true);

        if($checksum_got == $checksum)
            return $plaintext;
        else
            return false;
    }

}

class XIPHER{
}
