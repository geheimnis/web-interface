<?php
/*
 * Accepts new requests and queries on existing proceeding requests.
 *
 * Anyone, whether logged in or not, can use this page to submit requests.
 */
require(dirname(__FILE__) . "/_.php");

$task_manager = new TASK_MANAGER();

if($core_command = $__IO->get('core_command', true)){
    $result = null;
    $for_user_id = $__IO->get('for_user_id');
    switch($core_command){
        case 'identity-list':
            $result = $task_manager->create_task(
                $for_user_id,
                'identity-list'
            );
            break;
        case 'identity-test':
            $argv = json_encode(array(
                'title'=>$__IO->post('title'),
                'describe'=>$__IO->post('describe'),
            ));
            $result = $task_manager->create_task(
                $for_user_id,
                'identity-test',
                $argv
            );
            break;
        case 'identity-add':
            $argv = json_encode(array(
                'title'=>$__IO->post('title'),
                'describe'=>$__IO->post('describe'),
            ));
            $result = $task_manager->create_task(
                $for_user_id,
                'identity-add',
                $argv
            );
            break;
        case 'identity-delete': break;
        default:
            break;
    }
    if($result)
        $__IO->data($core_command, $result);
}

$__IO->output_JSON();
