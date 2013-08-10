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

    private $side = 'front';

    private $deny_access = false;
    
    public function __construct(){
        global $_SERVER;

        $this->configs = array(
            'INCPATH'=>dirname(__FILE__),
        );

        # prelimary decision of local visit. NOT reliable.
        $this->flags['local_visit'] = (
            ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') &&
            (substr($_SERVER['HTTP_HOST'],0,9) == 'localhost')
        );

        # read cookies
        $this->_cookies_read();

    }

    public function output_HTML($page_name){
        $this->_headers_write();
        $this->_cookies_write();
        $this->_output_HTML($page_name);
    }

    public function flag($query){
        if(array_key_exists($query, $this->flags)){
            return $this->flags[$query];
        } else {
            return null;
        }
    }

    public function set_side($side){
        if($side == 'end')
            $this->side = 'end';
        else
            $this->side = 'front';
    }

    public function set_access_deny(){
        $this->deny_access = true;
    }

    public function cookie($key, $value=null){
        if($value != null){
            $this->cookies[$key] = $value;
            $this->update_cookies = true;
            return $this;
        } else {
            if(array_key_exists($key, $this->cookies))
                return $this->cookies[$key];
            else
                return null;
        }
    }

    private function _cookies_read(){
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
                false
            ) == $cookie_check
        ){
            # then the cookie is regarded as trustful.
            $this->cookies = json_decode($cookie_data, true);
            $this->update_cookies = false;
        } else {
            $this->cookies = array();
            $this->update_cookies = true;
        }
    }

    private function _cookies_write(){
        global $_CONFIGS;
        if($this->update_cookies === false) return;

        $cookie_data = json_encode($this->cookies);
        $cookie_check = hash_hmac(
            $_CONFIGS['security']['cookie']['HMAC_algorithm'],
            $cookie_data,
            $_CONFIGS['security']['cookie']['sign_key'],
            false
        );

        $cookie_expire = ($_CONFIGS['security']['cookie']['life'] > 0)?
            (time() + $_CONFIGS['security']['cookie']['life']):0;

        setcookie(
            $_CONFIGS['names']['cookie']['data'],
            $cookie_data,
            $cookie_expire
        );

        setcookie(
            $_CONFIGS['names']['cookie']['check'],
            $cookie_check,
            $cookie_expire
        );
    }

    private function _headers_write(){
        if($this->deny_access)
            header('HTTP/1.1 401 Unauthorized', true, 401);
    }

    private function _output_HTML($page_name){
        global $_CONFIGS;

        $template_path = 
            $this->configs['INCPATH'] .
            '/../' .
            $_CONFIGS['template']['template_path']
        ;
        
        if(!$this->deny_access){
            $template_path .= '/' . ($this->side) . 'end';
            $page_name = 'index';
        }

        $loader = new Twig_Loader_Filesystem($template_path);
        $twig = new Twig_Environment($loader, array(
/*            'cache'=>
                $this->configs['INCPATH'] .
                '/../' .
                $_CONFIGS['template']['cache_path'],*/
            'debug'=>false,
        ));

        $template = $twig->loadTemplate($page_name . '.htm');
        echo $template->render(array());
    }

}

$__IO = new IO();
