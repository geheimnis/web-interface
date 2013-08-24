<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->require_login()
    ->declare_side('back')
    ->apply();

$task_manager = new TASK_MANAGER();

$__IO
    ->output_JSON()
;
