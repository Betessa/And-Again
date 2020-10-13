<?php

unset($SET);
global $SET;
$SET = new stdClass();



$SET->wget   = '/usr/local/bin/wget';
$SET->dot   = '/usr/local/bin/dot';





require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
