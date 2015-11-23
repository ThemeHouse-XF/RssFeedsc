<?php

class ThemeHouse_RssFeeds_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_RssFeeds' => array(
                'controller' => array(
                    'XenResource_ControllerPublic_Resource'
                ), 
            ), 
        );
    }

    public static function loadClassController($class, array &$extend)
    {
        $extend = self::createAndRun('ThemeHouse_RssFeeds_Listener_LoadClass', $class, $extend, 'controller');
    }
}