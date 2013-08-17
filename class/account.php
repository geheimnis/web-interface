<?php
class ACCOUNT{

    private $loaded = false;

    private $account_data = array(
        'id'=>null,
        'username_human'=>null,
        'username_php'=>null,
    );
    
    public function __construct($userid=null){
        global $_CONFIGS;
        if(is_numeric($userid)) $this->initialize($userid);
    }

    private function _is_legal_username($test){
        /*
         * Test if a username is legal.
         *
         * Caution: The input is NOT trim'ed, in calculating length or
         * testing charset.
         */
        global $_CONFIGS;
        $maxlen = $_CONFIGS['limits']['account']['max_length'];
        $minlen = $_CONFIGS['limits']['account']['min_length'];
        $charset = $_CONFIGS['limits']['account']['allow_chars'];

        $length = strlen($test);
        if($length >= $minlen && $length <= $maxlen){
            return ( 
                str_replace(
                    str_split($charset, 1),
                    '',
                    $test
                ) == ''
            );
        }

        return false;
    }

    private function initialize($data){
        global $__DATABASE;
        /*
         * Initialize this class.
         *
         * when $data is numeric, initialize this class from a record in
         * database with id equal to $data.
         * when $data is an array, read it and take its data in use, designed
         * for login and register process, which reduces a query of database.
         */
        $data_id = $data_username_human = $data_username_php = null;
        $this->loaded = false;

        if(is_numeric($data)){
            $result = $__DATABASE->select(
                'accounts',
                'id="' . $data . '"',
                'id,username,username_human'
            );
            if($row = $result->row()){
                $data_id = $row['id'];
                $data_username_human = base64_decode($row['username_human']);
                $data_username_php = $row['username'];
            }
        } else if(is_array($data)){
            $data_id = $data['id'];
            $data_username_human = base64_decode($data['username_human']);
            $data_username_php = $data['username'];
        }
        
        if(
            $this->_is_legal_username($data_username_human) &&
            (
                md5(strtolower(trim($data_username_human))) ==
                $data_username_php
            )
        ){
            $this->account_data = array(
                'id'=>$data_id,
                'username_human'=>$data_username_human,
                'username_php'=>$data_username_php,
            );
            $this->loaded = true;
        }

        return $this->loaded;
    }

    public function login($username, $passphrase){
        global $__DATABASE;
        $username_phped = md5(strtolower(trim($username)));
        
        $result = $__DATABASE->select(
            'accounts',
            'username="' . $username_phped . '"',
            'id,username_human,authproof'
        );
        $row = $result->row();

        $authresult = false;
        if($row)
            $authresult = 
                (true === $this->_passphrase_validate(
                    $passphrase,
                    $row['authproof']
                )
            );

        if(true === $authresult){
            return $this->initialize(array(
                'id'=>$row['id'],
                'username'=>$username_phped,
                'username_human'=>$row['username_human'],
            ));
        }

        return false;
    }

    public function create($username, $passphrase){
        /*
         * Create an account.
         *
         * Meanings of return values:
         *   true: successfully created.
         *     -1: invalid username
         *     -2: username already exists.
         *     -3: failed inserting into database.
         *  false: error initializing a account.
         */

        global $__DATABASE;
        $username_trimed = trim($username);
        if(!$this->_is_legal_username($username_trimed)) return -1;
        $username_human = base64_encode($username_trimed);

        $username_phped = md5(strtolower(trim($username)));
        
        $result = $__DATABASE->select(
            'accounts',
            'username="' . $username_phped . '"',
            'id'
        );
        $row = $result->row();

        if($row) return -2;

        $result = $__DATABASE->insert(
            'accounts',
            array(
                'username'=>$username_phped,
                'username_human'=>$username_human,
                'authproof'=>$this->_passphrase_store($passphrase),
            )
        );

        if($insert_id = $result->insert_id()){
            return $this->initialize($insert_id);
        }

        return -3;
    }

    public function get($item){
        if(!$this->loaded) return null;
        if(!array_key_exists($item, $this->account_data)) return null;
        return $this->account_data[$item];
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

            var_dump($parts);
            print '<br />';

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
