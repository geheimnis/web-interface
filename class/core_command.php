<?php
class COMMAND_CONTACT{
    
    private $parent = null;

    public function __construct($parent){
        $this->parent = $parent;
    }

    public function test($title, $describe){
        $composed = json_encode(array(
            'title'=>$title,
            'describe'=>$describe,
        ));
        return $this->parent->execute(
            'identity-test',
            bin2hex($composed)
        );
    }

    public function list_all($allow_cache=true){
        global $__CACHE;
        $result = '';
        if($allow_cache && $cached = $__CACHE->item('contact-list'))
            $result = $cached;
        else {
            $result = $this->parent->execute('identity-list');
            $__CACHE->item('contact-list', $result);
        }
        return $result;
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

    private function parse($result){
        $ret = $item = array();
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
                        'signal'=>$label_leading,
                        'code'=>$label_number,
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

    public function optimize(){
        global $__KEYRING;
        $access_key = bin2hex(
            $__KEYRING->get('database-access-key')
        );
        $user_identifier = $this->get_user_identifier();


        $command = implode(
            ' ',
            array(
                'python',
                $this->basepath . 'invoke.py',
                $user_identifier,
                $access_key,
                'optimize',
            )
        );

        exec($command);
    }

    public function query($query_id){
        global $__KEYRING;
        $access_key = bin2hex(
            $__KEYRING->get('database-access-key')
        );
        $user_identifier = $this->get_user_identifier();

        $query_id = strtolower(trim($query_id));
        if(!(
            strlen($query_id) == 36 &&
            str_replace(
                str_split('0123456789-abcdef'),
                '',
                $query_id
            ) == ''
        ))
            return false;
        

        $command = implode(
            ' ',
            array(
                'python',
                $this->basepath . 'invoke.py',
                $user_identifier,
                $access_key,
                'query',
                $query_id,
            )
        );

        $result = array();
        exec($command, $result);
        return $this->parse($result);
    }

    public function execute($command_name, $arg=null, &$result_query_id=null){
        /*
         * Execute a command
         *
         * 1)Returns a string, if the command executed returns immediate
             result.
         * 2)Returns True, if command needs to be async queried for result. In
         *   this case, use $result_query_id to get it.
         * 3)Returns False, when there is an error.
         */
        global $__KEYRING;
        $access_key = bin2hex(
            $__KEYRING->get('database-access-key')
        );
        $user_identifier = $this->get_user_identifier();

        $composed = bin2hex(
            json_encode(
                array(
                    'cmd'=>$command_name,
                    'arg'=>$arg,
                )
            )
        );

        $command = implode(
            ' ',
            array(
                'python',
                $this->basepath . 'invoke.py',
                $user_identifier,
                $access_key,
                $composed,
            )
        );

        $result = array();
        exec($command, $result);
        
        $parsed_result = $this->parse($result);

        if(
            count($parsed_result) == 1 &&
            $parsed_result[0]['code'] == '202'
        ){
            $result_query_id = $parsed_result[0]['data'];
            return True;
        } else if($parsed_result[0]['signal'] == '+')
            return $parsed_result[0]['data'];
        else
            return False;
    }

}

$__CORE_COMMAND = new CORE_COMMAND();
