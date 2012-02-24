<?php

    /**
     * The path to the Aerial www folder
     *
     * @see /projectroot/www
     */
    Configuration::set("WWW_PATH", dirname(__FILE__)."/../www");

    /**
     * The path to the Aerial library folder
     *
     * @see /projectroot/lib
     */
    Configuration::set("LIBRARY_PATH", dirname(__FILE__) . "/../lib");

    /**
     * Debug mode - show debugging messages and data if TRUE
     */
    Configuration::set("DEBUG_MODE", true);

    Configuration::set("DB_ENGINE", "mysql");
    Configuration::set("DB_HOST", "localhost");
    Configuration::set("DB_SCHEMA", "");
    Configuration::set("DB_USERNAME", "");
    Configuration::set("DB_PASSWORD", "");
    Configuration::set("DB_PORT", "3306");

    Configuration::set("PHP_MODELS", Configuration::get("WWW_PATH")."/../src_php/models");

    // check whether LIBRARY PATH is valid, as it will be needed shortly
    if(!realpath(Configuration::get("LIBRARY_PATH")))
        throw new Exception("Library path not valid: ".Configuration::get("LIBRARY_PATH"));

    function import($classPath)
    {
    	$importFile = str_replace(".", DIRECTORY_SEPARATOR, $classPath) . ".php";
    	require_once(Configuration::get("LIBRARY_PATH") . DIRECTORY_SEPARATOR . $importFile);
    }

    class Configuration
    {
        private static $definitions = array();

        // do not allow instantiation
        final private function __construct(){}

        public static function set($name, $value)
        {
            self::$definitions[$name] = $value;
        }

        public static function get($name)
        {
            return isset(self::$definitions[$name]) ? self::$definitions[$name] : null;
        }

    }