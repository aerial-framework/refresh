<?php
    require_once(Configuration::get("LIBRARY_PATH")."/aerialframework/core/Bootstrapper.php");

    class Bootstrap
    {
        public static function actAsServer($override = null)
        {
            $libraryPath = Configuration::get("LIBRARY_PATH");
            if(empty($libraryPath) || !realpath($libraryPath))
                throw new Exception("LIBRARY_PATH is invalid.");

            if(!empty($override))
                $contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : null;
            else
                $contentType = $override;

            switch($contentType)
            {
                default:
                case "application/json":
                    Bootstrapper::getInstance();

                    include_once($libraryPath."/servers/rest/index.php");
                    break;
                case "application/x-amf":
                    include_once($libraryPath."/servers/amfphp/gateway.php");
                    break;
            }
        }
    }
