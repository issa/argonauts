<?php
// Here you can initialize variables that will be available to your tests

global $STUDIP_BASE_PATH, $ABSOLUTE_URI_STUDIP, $CACHING_ENABLE, $CACHING_FILECACHE_PATH;

// common set-up, usually done by lib/bootstraph.php and
// config/config_local.inc.php when run on web server
if (!isset($STUDIP_BASE_PATH)) {
    $STUDIP_BASE_PATH = '/Projects/studip/svn_goe/branches/3.4';
    $ABSOLUTE_PATH_STUDIP = $STUDIP_BASE_PATH.'/public/';
}

$CACHING_ENABLE = false;
//$CACHING_FILECACHE_PATH = '/tmp';

// set include path
$inc_path = ini_get('include_path');
$inc_path .= PATH_SEPARATOR.$STUDIP_BASE_PATH;
$inc_path .= PATH_SEPARATOR.$STUDIP_BASE_PATH.'/config';
ini_set('include_path', $inc_path);

require 'lib/functions.php';

// Setup autoloading
require 'lib/classes/StudipAutoloader.php';
StudipAutoloader::register();

// General classes folders
StudipAutoloader::addAutoloadPath($GLOBALS['STUDIP_BASE_PATH'].'/lib/models');
StudipAutoloader::addAutoloadPath($GLOBALS['STUDIP_BASE_PATH'].'/lib/classes');

// Plugins
StudipAutoloader::addAutoloadPath($GLOBALS['STUDIP_BASE_PATH'].'/lib/plugins/core');
StudipAutoloader::addAutoloadPath($GLOBALS['STUDIP_BASE_PATH'].'/lib/plugins/db');
StudipAutoloader::addAutoloadPath($GLOBALS['STUDIP_BASE_PATH'].'/lib/plugins/engine');

// Messy file names
StudipAutoloader::addClassLookups(
    array(
        'StudipPlugin' => $GLOBALS['STUDIP_BASE_PATH'].'/lib/plugins/core/StudIPPlugin.class.php', )
);
