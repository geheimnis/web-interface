<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->declare_side('back')
    ->apply()
;

# template logical data
$template_show_warning = true;
$template_tab = 'login';
$template_message_id = 1; # defined in template.

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
    ->data('option_alert_message', $template_message_id)
;

$__IO->output_HTML('login');
