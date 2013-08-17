<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->declare_side('back')
    ->apply()
;

# template logical data
$template_show_warning = true;
$template_tab = 'login';

# login or register logic.
switch($__IO->get('do',true)){
    case 'login':
        $template_show_warning = false;
        break;
    case 'reg':
        $template_show_warning = false;
        $template_tab = 'reg';
        break;
    default:
        break;
}

$__IO
    ->data('option_show_warning', $template_show_warning)
    ->data('option_tab', $template_tab)
;

$__IO->output_HTML('login');
