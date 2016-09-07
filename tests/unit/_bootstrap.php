<?php
// Here you can initialize variables that will be available to your tests

global $STUDIP_BASE_PATH, $ABSOLUTE_URI_STUDIP, $CACHING_ENABLE;

// set error reporting
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);

// common set-up, usually done by lib/bootstraph.php and
// config/config_local.inc.php when run on web server
if (!isset($STUDIP_BASE_PATH)) {
    $STUDIP_BASE_PATH = '/Projects/studip/goe-git';
    $ABSOLUTE_PATH_STUDIP = $STUDIP_BASE_PATH . '/public/';
}

$CACHING_ENABLE = false;

// set include path
$inc_path = ini_get('include_path');
$inc_path .= PATH_SEPARATOR . $STUDIP_BASE_PATH;
$inc_path .= PATH_SEPARATOR . $STUDIP_BASE_PATH . '/config';
ini_set('include_path', $inc_path);
