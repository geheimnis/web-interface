<?php
$_INCPATH = dirname(__FILE__);

# load configurations
require("$_INCPATH/config/_.php");


# initialize classes
require("$_INCPATH/lib/Twig/Autoloader.php");
Twig_Autoloader::register();

require("$_INCPATH/class/_.php");
