<?php
class COMMAND_CONTACT{
    
    private $parent = null;

    public function __construct($parent){
        $this->parent = $parent;
    }

    private function _base_command(){
        global $__KEYRING;
        $access_key = bin2hex(
            $__KEYRING->get('database-access-key')
        );
        $user_identifier = $this->parent->get_user_identifier();

        return implode(
            ' ',
            array('identity.py', $user_identifier, $access_key)
        );
    }
    
    public function test($title, $describe){
        $composed = json_encode(array(
            'title'=>$title,
            'describe'=>$describe,
        ));
        return $this->parent->parse(
            $this->parent->execute(
                implode(' ', array(
                    $this->_base_command(),
                    'test',
                    $composed
                ))
            )
        );
    }

    public function list_all($allow_cache=true){
        global $__CACHE;
        $result = '';
        if($allow_cache && $cached = $__CACHE->item('contact-list'))
            $result = $cached;
        else {
            $command = implode(' ', array(
                $this->_base_command(),
                'list',
            ));

            $result = $this->parent->execute($command);
            $__CACHE->item('contact-list', $result);
        }
        return $this->parent->parse($result);
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

    public function parse($result){
        $ret = $item = array();
        $result = explode('\n', $result);
        foreach($result as $line){
            $line = strstr(trim($line), '[', false);
            if($line){
                if(false === $pos = strpos($line, ']', 0)) continue;
                
                $label = substr($line, 1, $pos-1);
                $data = trim(substr($line, $pos+1));

                $label_leading = substr($label, 0, 1);
                $label_number = substr($label, 1);
                if(!(
                    in_array($label_leading, array('+', 'X')) &&
                    (
                        $label_number == '' ||
                        is_numeric($label_number)
                    )
                ))
                    continue;
                
                if(
                    $item && 
                    $item['signal'] == $label_leading &&
                    $item['code'] == $label_number
                ){
                    $item['data'][] = $data;
                } else {
                    if($item){
                        $item['data'] = implode('\n', $item['data']);
                        $ret[] = $item; 
                    }
                    $item = array(
                        'signal'=>'',
                        'code'=>'',
                        'data'=>array(
                            $data,
                        ),
                    );
                }
            }
        }
        $item['data'] = implode('\n', $item['data']);
        $ret[] = $item; 

        return $ret;
    }

    public function execute($command){
        $result = array();
        $command = 'python ' . $this->basepath . $command;
        exec($command, $result);
        return implode('\n', $result);
    }

}

$__CORE_COMMAND = new CORE_COMMAND();
