<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->declare_side('back')
    ->apply();

# login or register logic.


$__IO->output_HTML('login');
