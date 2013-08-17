<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->declare_side('back')
    ->apply()
;

$template_show_warning = true;

# login or register logic.
switch($__IO->get('do',true)){
    case 'login':
        $template_show_warning = false;
        break;
    case 'reg':
        $template_show_warning = false;
        break;
    default:
        break;
}

$__IO
    ->data('option_show_warning', $template_show_warning)
;

$__IO->output_HTML('login');
