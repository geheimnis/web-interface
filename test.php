<?php
require(dirname(__FILE__) . "/_.php");
/*
 * Run tests
 */

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

print "Test begin at timestamp as " . microtime_float() . "<hr />";
print str_replace("\n", "<br />",$__CORE_COMMAND->test());
print "<hr />Test end at timestamp as " . microtime_float();
