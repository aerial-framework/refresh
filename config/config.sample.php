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
     * The path to the Aerial library folder
     *
     * @see /projectroot/config
     */
    Configuration::set("CONFIG_PATH", dirname(__FILE__));

    /**
     * Debug mode - show debugging messages and data if TRUE
     */
    Configuration::set("DEBUG_MODE", true);

    /**
     * The default encoding of the response data
     */
    Configuration::set("DEFAULT_CONTENT_TYPE", "application/json");

    /**
     * GZIP compression of output data
     */
    Configuration::set("GZIP_ENABLED", false);

    Configuration::set("DB_ENGINE", "mysql");
    Configuration::set("DB_HOST", "localhost");
    Configuration::set("DB_SCHEMA", "");
    Configuration::set("DB_USERNAME", "");
    Configuration::set("DB_PASSWORD", "");
    Configuration::set("DB_PORT", "3306");

    Configuration::set("PHP_MODELS", Configuration::get("WWW_PATH")."/../src_php/org/aerialframework/vo");
    Configuration::set("PHP_SERVICES", Configuration::get("WWW_PATH")."/../src_php/org/aerialframework/service");

    // check whether LIBRARY PATH is valid, as it will be needed shortly
    if(!realpath(Configuration::get("LIBRARY_PATH")))
        throw new Exception("Library path not valid: ".Configuration::get("LIBRARY_PATH"));

	include_once dirname(__FILE__)."/require.php";

	Configuration::set("COLLECTION_CLASS", "flex.messaging.io.ArrayCollection");
	Configuration::set("DOCTRINE_DEFAULT_HYDRATION", Doctrine_Core::HYDRATE_RECORD);


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