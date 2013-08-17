<?php
require(dirname(__FILE__) . "/_.php");

$__FIREWALL
    ->declare_side('back')
    ->apply()
;

# template logical data
$template_show_warning = true;
$template_tab = 'login';
$template_message_id = -1; # defined in template.
$template_allow_reg = $_CONFIGS['security']['session']['allow_new_register'];

# login or register logic.
switch($__IO->get('do',true)){
    case 'login':
        $result = $__SESSION_MANAGER->login(
            $__IO->post('username'),
            $__IO->post('password')
        );
        if(!$result){
            $template_message_id = 1;

        }
        $template_show_warning = false;
        break;
    case 'reg':
        if(false === $template_allow_reg){
        }
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
    ->data('option_allow_register', $template_allow_reg)

    ->output_HTML('login')
;
