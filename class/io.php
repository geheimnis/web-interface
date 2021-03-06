<?php

class IO{

    private $flags = array(
        'local_visit'=>false,
    );

    private $configs = array(
        'INCPATH'=>null,
    );

    private $cookies = array();
    private $output_data = array();
    private $posts = array();
    private $gets = array();
    private $update_cookies = false;

    private $side = 'front';

    private $deny_access = false;
    private $forced_login = false;
    
    public function __construct(){
        global $_SERVER;

        $this->configs = array(
            'INCPATH'=>dirname(__FILE__),
        );

        # prelimary decision of local visit. NOT reliable.
        $this->flags['local_visit'] = (
            ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') &&
            (
                substr($_SERVER['HTTP_HOST'],0,9) == 'localhost' ||
                substr($_SERVER['HTTP_HOST'],0,9) == '127.0.0.1'
            )
        );

        # read
        $this->_cookies_read();
        $this->_posts_read();
        $this->_gets_read();

    }

    public function output_HTML($page_name, $suffix='.htm'){
        $this->_headers_write();
        $this->_cookies_write();
        $this->_output_HTML($page_name, $suffix);
        return $this;
    }

    public function output_JSON(){
        $this->_cookies_write();
        $this->_output_JSON();
        return $this;
    }

    public function flag($query){
        if(array_key_exists($query, $this->flags)){
            return $this->flags[$query];
        } else {
            return null;
        }
    }

    public function set_side($side){
        if($side == 'back')
            $this->side = 'back';
        else
            $this->side = 'front';
    }

    public function set_access_deny(){
        $this->deny_access = true;
    }

    public function set_force_login(){
        $this->forced_login = true;
    }

    public function cookie($key, $value=null){
        if($value !== null){
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

    public function data($key, $value=null){
        if($value !== null){
            $this->output_data[$key] = $value;
            return $this;
        } else {
            if(array_key_exists($key, $this->output_data))
                return $this->output_data[$key];
            else
                return null;
        }
    }

    public function post($key, $normalize=false){
        if(array_key_exists($key, $this->posts))
            return ($normalize?
                strtolower(trim($this->posts[$key])):
                $this->posts[$key]
            );
        return null;
    }

    public function get($key, $normalize=false){
        if(array_key_exists($key, $this->gets))
            return ($normalize?
                strtolower(trim($this->gets[$key])):
                $this->gets[$key]
            );
        return null;
    }

    public function stop(){
        exit();
    }

    private function _posts_read(){
        global $_POST;
        $this->posts = $_POST;
    }

    private function _gets_read(){
        global $_GET;
        $this->gets = $_GET;
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
        if($this->deny_access === true) return;

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

        return true;
    }

    private function _headers_write(){
        global $__SESSION_MANAGER;
        if($this->deny_access){
            header('HTTP/1.1 401 Unauthorized', true, 401);
            return false;
        } else if($this->forced_login){
            if($__SESSION_MANAGER->token->is_loaded() === false){
                header("Location: login.php");
                return false;
            }
        }
        return true;
    }

    private function _output_JSON(){
        global $__SESSION_MANAGER;
        if($this->deny_access){
            echo "{}";
            return;
        };

        if(!(
            $this->forced_login &&
            $__SESSION_MANAGER->token->is_loaded() === false
        )){
            echo json_encode($this->output_data);
        } else
            echo "{}";
    }

    private function _output_HTML($page_name, $suffix){
        global $_CONFIGS,$__SESSION_MANAGER;

        $template_path = 
            $this->configs['INCPATH'] .
            '/../' .
            $_CONFIGS['template']['template_path']
        ;
        if(!$this->deny_access)
            $template_path .= '/' . ($this->side) . 'end';
        else
            $page_name = 'index';

        if(!(
            $this->forced_login &&
            $__SESSION_MANAGER->token->is_loaded() === false
        )){
            $loader = new Twig_Loader_Filesystem($template_path);
            $twig = new Twig_Environment($loader, array(
                /* 'cache'=>
                    $this->configs['INCPATH'] .
                    '/../' .
                    $_CONFIGS['template']['cache_path'],*/
                'debug'=>false,
            ));

            $template = $twig->loadTemplate($page_name . $suffix);
            echo $template->render($this->output_data);
        }
    }

}

$__IO = new IO();
