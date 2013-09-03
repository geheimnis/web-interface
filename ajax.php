<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->require_login()
    ->declare_side('back')
    ->apply();

$task_manager = new TASK_MANAGER();
$data_navbar = array(
    'unread_task'=>$task_manager->get_tasks_unread_count(),
);

if($core_command = $__IO->get('core', true)){
    $core_operand = $__IO->get('operand', true);
    $result = null;
    switch($core_command){
        case 'contact':
            if($core_operand == 'list')
                $result = $__CORE_COMMAND->contact->list_all();
            break;
        default:
            break;
    }
    if($result)
        $__IO->data($core_command, $result);
}

$__IO
    ->data('navbar', $data_navbar)
    ->output_JSON()
;
