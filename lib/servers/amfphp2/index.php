<?php
    /**
     *  This file is part of amfPHP
     *
     * LICENSE
     *
     * This source file is subject to the license that is bundled
     * with this package in the file license.txt.
     * @package Amfphp
     */

    /**
     *  includes
     *  */
    require_once dirname(__FILE__) . '/Amfphp/ClassLoader.php';

    $config = new Amfphp_Core_Config();
    $config->serviceFolderPaths = array(realpath(Configuration::get("PHP_SERVICES")));
    $config->serviceNames2ClassFindInfo["TestService"] = new Amfphp_Core_Common_ClassFindInfo(Configuration::get("PHP_SERVICES")."/TestService.php", "TestService");
    $gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway($config);

    $gateway->service();
    $gateway->output();


?>
