<?php

class IO{

    private $flags = array(
        'local_visit'=>false,
    );

    private $configs = array(
        'template'=>null,
        'INCPATH'=>null,
    );
    
    public function __construct(){
        global $_SERVER, $_COOKIE, $_CONFIGS;

        $this->configs = array(
            'template'=>$_CONFIGS['template'],
            'INCPATH'=>dirname(__FILE__),
        );

        # prelimary decision of local visit. NOT reliable.
        $this->flags['local_visit'] == (
            $_SERVER['REMOTE_ADDR'] == '127.0.0.1' &&
            $_SERVER['HTTP_HOST'] == 'localhost'
        );

    }

    public function getFlag($query){
        if(in_array($query, $this->flags, true)){
            return $this->flags[$query];
        } else {
            return null;
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
