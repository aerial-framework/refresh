<?php

    /**
     * The path to the Aerial www folder
     *
     * @see /projectroot/www
     */
    define("WWW_PATH", dirname(__FILE__)."/../www");

    /**
     * The path to the Aerial library folder
     *
     * @see /projectroot/lib
     */
    define("LIBRARY_PATH", dirname(__FILE__) . "/../lib");

    /**
     * Debug mode - show debugging messages and data if TRUE
     */
    define("DEBUG_MODE", true);

    define("DB_ENGINE", "mysql");
    define("DB_HOST", "localhost");
    define("DB_SCHEMA", "");
    define("DB_USERNAME", "");
    define("DB_PASSWORD", "");
    define("DB_PORT", "3306");

    define("PHP_MODELS", WWW_PATH."/../src_php/models");

    // check whether LIBRARY PATH is valid, as it will be needed shortly
    if(!realpath(LIBRARY_PATH))
        throw new Exception("Library path not valid: ".LIBRARY_PATH);

    function import($classPath)
    {
    	$importFile = str_replace(".", DIRECTORY_SEPARATOR, $classPath) . ".php";
    	require_once(LIBRARY_PATH . DIRECTORY_SEPARATOR . $importFile);
    }