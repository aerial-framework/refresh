<?php
    require_once(LIBRARY_PATH."/aerialframework/core/Bootstrapper.php");

    class Bootstrap
    {
        public static function actAsServer($override = null)
        {
            if(!defined("LIBRARY_PATH") || !realpath(LIBRARY_PATH))
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

                    include_once(LIBRARY_PATH."/servers/rest/index.php");
                    break;
                case "application/x-amf":
                    include_once(LIBRARY_PATH."/servers/amfphp/gateway.php");
                    break;
            }
        }
    }
