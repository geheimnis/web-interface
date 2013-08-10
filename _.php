<?php
$_INCPATH = dirname(__FILE__);

# load configurations
require("$_INCPATH/config/_.php");


# initialize classes
require("$_INCPATH/lib/Twig/Autoloader.php");
Twig_Autoloader::register();

require("$_INCPATH/class/database.php");
require("$_INCPATH/class/io.php");
require("$_INCPATH/class/session_manager.php");
require("$_INCPATH/class/core_command.php");
require("$_INCPATH/class/cipher.php");
