<?php
/*
 * This is not a regular config part file like others.
 *
 * This file is a script, loading Geheimnis system global config file and
 * use its values to overwrite some default values already set in $_CONFIGS
 * array.
 */

# Where the config file is.
$___GEHEIMNIS_PATH = dirname(__FILE__) . '/gaconfig.ini.php';

$___GEHEIMNIS_CONFIGS = parse_ini_file($___GEHEIMNIS_PATH, true);

$_CONFIGS['names']['general']['system'] =
    $___GEHEIMNIS_CONFIGS['general']['name'];
$_CONFIGS['security']['session']['allow_new_register'] =
    $___GEHEIMNIS_CONFIGS['security']['new_account_register'];

$_CONFIGS['geheimnis'] = array(
    'config_path' => $___GEHEIMNIS_PATH,
    'core_relpath' => $___GEHEIMNIS_CONFIGS['path']['core_commands'],
);
