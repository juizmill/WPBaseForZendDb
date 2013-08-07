<?php
/**
 * Created by JetBrains PhpStorm.
 * User: juizmill
 * Date: 06/08/13
 * Time: 21:36
 * To change this template use File | Settings | File Templates.
 */

namespace WPBase;

use Zend\Mvc\MvcEvent;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}