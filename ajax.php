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

/*
 * AJAX is organized as 'namespace'. A key in AJAX's root is being treated by
 * JavaScript as a 'namespace' and passed to related handlers.
 */

$__IO
    ->data('approval', $data_approval)
    ->output_JSON()
;
