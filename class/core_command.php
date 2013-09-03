<?php
class COMMAND_CONTACT{
    
    private $parent = null;

    public function __construct($parent){
        $this->parent = $parent;
    }

    public function list_all(){
        global $__KEYRING, $__SESSION_MANAGER;
        $access_key = bin2hex(
            $__KEYRING->get('database-access-key')
        );

        $user_identifier = $this->parent->get_user_identifier();

        return print_r($access_key,true);

    }

}

class CORE_COMMAND{
    
    private $basepath = null;

    public $contact = null;
    
    public function __construct(){
        global $_CONFIGS;
        $basepath = trim(
            dirname($_CONFIGS['geheimnis']['config_path']) .
            '/../' . # exiting config/ path.
            $_CONFIGS['geheimnis']['core_relpath']
        );

        $empty_leading = 
            (substr($basepath,0,1) == '/')?
                '/':    # for absoulte path
                './';    # for relative path

        if(substr($basepath, -1) == '/')
            $basepath = substr($basepath, 0, -1);

        if($basepath == '')
            $basepath = $empty_leading;
        else
            $basepath .= "/";

        $this->basepath = $basepath;

        $this->contact = new COMMAND_CONTACT($this);
    }

    public function get_user_identifier(){
        global $__SESSION_MANAGER;
        if(null !== $__SESSION_MANAGER->token){
            if(false !== $userid = $__SESSION_MANAGER->token->get_user_id()){
                $account = new ACCOUNT($userid);
                return $account->get('username_php');
            }
        }
        return false;
    }

    public function test(){
        return $this->_execute('test.py --argument "hi"');
    }

    private function _execute($command){
        $result = array();
        $command = 'python ' . $this->basepath . $command;
        exec($command, $result);
        return implode("\n", $result);
    }

}

$__CORE_COMMAND = new CORE_COMMAND();
