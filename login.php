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
        $template_tab = 'reg';
        if(true === $template_allow_reg){
            $username = $__IO->post('username');
            $password = $__IO->post('password');
            $password2 = $__IO->post('password2');
            if($password2 != $password)
                $template_message_id = 2;
            else {
                $account = new ACCOUNT();
                $result = $account->create($username, $password);
            
                if($result === true){
                    $template_tab = 'login';
                    $template_message_id = 7;
                } else {
                    $code_map = array(
                        -1=>3,
                        -2=>4,
                        -3=>5,
                        false=>6,
                    );
                    $template_message_id = $code_map[$result];
                }
                    
            }
        }
        $template_show_warning = false;
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
