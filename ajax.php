<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->require_login()
    ->declare_side('back')
    ->apply();

$task_manager = new TASK_MANAGER();
$data_approval = array(
    'unread_task'=>$task_manager->get_tasks_unread_count(),
);

if($core_command = $__IO->get('core', true)){
    $core_operand = $__IO->get('operand', true);
    $result = null;
    switch($core_command){
        case 'identity':
            switch($core_operand){
                case 'list':
                    $result = $task_manager->create_task('identity-list');
                    break;
                case 'test':
                    $result = $__CORE_COMMAND->contact->test(
                        $__IO->post('title'),
                        $__IO->post('describe')
                    );
                    break;
                case 'add': break;
                case 'delete': break;
                default:
                    break;
            }
            break;
        default:
            break;
    }
    if($result)
        $__IO->data($core_command, $result);
}

/*
 * AJAX is organized as 'namespace'. A key in AJAX's root is being treated by
 * JavaScript as a 'namespace' and passed to related handlers.
 */

$__IO
    ->data('approval', $data_approval)
    ->output_JSON()
;
