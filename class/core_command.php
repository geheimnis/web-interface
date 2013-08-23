<?php
class CORE_COMMAND{
    
    private $basepath = null;
    
    public function __construct(){
        global $_CONFIGS;
        $basepath = trim(
            $_CONFIGS['geheimnis']['config_path'] .
            '/' .
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
    }

    private function _execute($command){
        $result = array();
        $command = 'python ' . $this->basepath . $command;
        exec($command, $result);
        return implode("\n", $result);
    }

}

$__core_command = new CORE_COMMAND();
