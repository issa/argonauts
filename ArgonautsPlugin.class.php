<?php

/**
 * ArgonautsPlugin.class.php
 *
 * ...
 *
 * @author  Issa <issa@luniki.de>
 * @version 0.1.0
 */

class ArgonautsPlugin extends \StudIPPlugin implements \SystemPlugin {

    public function __construct() {
        parent::__construct();
    }

    static function onEnable($id)
    {
        // enable nobody role by default
        \RolePersistence::assignPluginRoles($id, array(7));
    }

    static function onDisable($id)
    {
    }

}
