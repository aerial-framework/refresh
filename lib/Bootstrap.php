<?php
    class Bootstrap
    {
        public static function actAsServer($override = null)
        {
            if(!empty($override))
                $contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : null;
            else
                $contentType = $override;

            switch($contentType)
            {
                default:
                case "application/json":
                    include_once(get_include_path()."/servers/rest/index.php");
                    break;
                case "application/x-amf":
                    include_once(get_include_path()."/servers/amfphp/gateway.php");
                    break;
            }
        }
    }
