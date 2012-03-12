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

    $classes = getClassesAndInfo();

    $config = new Amfphp_Core_Config();
    $config->serviceFolderPaths = array(realpath(Configuration::get("PHP_SERVICES")));
    $config->serviceNames2ClassFindInfo = $classes;
    $gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway($config);

    $gateway->service();
    $gateway->output();

    function getClassesAndInfo()
    {
        $phpServicesDir = Configuration::get("PHP_SERVICES");
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($phpServicesDir), RecursiveIteratorIterator::LEAVES_ONLY);
        $classes = array();

        foreach($files as $file)
        {
            if(empty($file))
                continue;

            $e = explode('.', $file->getFileName());
            if(empty($e) || count($e) < 2)
                continue;

            $path = $file->getRealPath();
            $className = $e[0];
            $extension = $e[1];

            if($extension != "php")
                continue;

            require_once($path);
            $classes[$className] = new Amfphp_Core_Common_ClassFindInfo($path, $className);
        }

        return $classes;
    }

?>
