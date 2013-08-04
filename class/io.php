<?php

class IO{

    private $flags = array(
        'local_visit'=>false,
    );

    private $configs = array(
        'INCPATH'=>null,
    );

    private $cookies = array();
    private $update_cookies = false;
    
    public function __construct(){
        global $_SERVER;

        $this->configs = array(
            'INCPATH'=>dirname(__FILE__),
        );

        # prelimary decision of local visit. NOT reliable.
        $this->flags['local_visit'] == (
            $_SERVER['REMOTE_ADDR'] == '127.0.0.1' &&
            $_SERVER['HTTP_HOST'] == 'localhost'
        );

        # read cookies
        $this->_read_cookies();

    }

    public function getFlag($query){
        if(in_array($query, $this->flags, true)){
            return $this->flags[$query];
        } else {
            return null;
        }
    }

    private function _read_cookies(){
        global $_COOKIE, $_CONFIGS;
        $name_data = $_CONFIGS['names']['cookie']['data'];
        $name_check = $_CONFIGS['names']['cookie']['check'];

        $cookie_data = 
            isset($_COOKIE[$name_data])?$_COOKIE[$name_data]:false;
        $cookie_check =
            isset($_COOKIE[$name_check])?$_COOKIE[$name_check]:false;

        if(
            hash_hmac(
                $_CONFIGS['security']['cookie']['HMAC_algorithm'],
                $cookie_data,
                $_CONFIGS['security']['cookie']['sign_key'],
                false,
            ) == $cookie_check
        ){
            # then the cookie is regarded as trustful.
        }

    }

    private function _outputHTML(){
        $loader = new Twig_Loader_Filesystem(
            $this->configs['INCPATH'] .
            '../' .
            $this->configs['template']['template_path']
        );
        $twig = new Twig_Environment($loader, array(
            'cache' =>
                $this->configs['INCPATH'] .
                '../' .
                $this->configs['template']['cache_path'],
        ));
    }

}
