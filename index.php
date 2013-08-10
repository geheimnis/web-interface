<?php
require(dirname(__FILE__) . "/_.php");

$want_side = 'front';
if(
    ($__IO->flag('local_visit') === true) &&
    ($__IO->cookie('frontend') != 'y')
)
    $want_side = 'back';

$__FIREWALL
    ->declare_side($want_side)
    ->apply();
    
$__IO->output_HTML('index');
