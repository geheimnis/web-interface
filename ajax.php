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

$__IO
    ->data('navbar', $data_navbar)
    ->output_JSON()
;
