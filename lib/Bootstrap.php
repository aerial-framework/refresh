<?php
    require_once(Configuration::get("LIBRARY_PATH") . "/aerialframework/core/Bootstrapper.php");
    include_once(Configuration::get("LIBRARY_PATH") . "/aerialframework/utils/Utils.php");

    class Bootstrap
    {
        public static function actAsServer($override = null)
        {
            // instantiate the Aerial bootstrapper
            Bootstrapper::getInstance();

            $libraryPath = Configuration::get("LIBRARY_PATH");
            if(empty($libraryPath) || !realpath($libraryPath))
                throw new Exception("LIBRARY_PATH is invalid.");

            if(empty($override))
                $contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : null;
            else
                $contentType = $override;

            switch($contentType)
            {
                default:
                    include_once($libraryPath . "/servers/rest/index.php");
                    break;
                case "application/x-amf":
                    import("aerialframework.service.AbstractService");
                    include_once($libraryPath . "/servers/amfphp2/index.php");
                    break;
            }
        }

        public static function actAsLibrary()
        {
            // instantiate the Aerial bootstrapper
            Bootstrapper::getInstance();
        }
    }
