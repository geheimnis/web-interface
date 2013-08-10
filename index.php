<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->declare_side(null)
    ->apply();

$__IO->output_HTML('index');
