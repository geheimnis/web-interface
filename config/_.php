<?php
$_CONFIGS = array();
$_CONFIGS_INCPATH = dirname(__FILE__);

require("$_CONFIGS_INCPATH/security.php");
require("$_CONFIGS_INCPATH/template.php");
require("$_CONFIGS_INCPATH/names.php");
require("$_CONFIGS_INCPATH/limits.php");
require("$_CONFIGS_INCPATH/database.php");

# overwrites some values from global system control
require("$_CONFIGS_INCPATH/geheimnis.php");
