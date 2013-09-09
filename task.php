<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->require_login()
    ->declare_side('back')
    ->apply();

$task_manager = new TASK_MANAGER();

switch($__IO->get('do', true)){
    case 'describe':
        $id = $__IO->get('id');
        $js = $__IO->get('js');
        $task = new TASK($id);
        
        $__IO
            ->data('command_name', $task->get_command_name())
            ->data('command_arg', $task->get_command_arg())
            ->output_HTML('task.desc', ($js == 'y'? '.js':'.htm'))
            ->stop()
        ;
        break;
    default:
        break;
}

$__IO
    ->output_HTML('task')
;
