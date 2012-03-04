<?php

    if(!function_exists("is_undefined"))
    {
        function is_undefined($obj)
        {
            return is_object($obj) ? get_class($obj) == "undefined" : false;
        }
    }

    // load the configuration file
    require_once("../config/config.php");

    // load the Bootstrap file
    require_once("../lib/Bootstrap.php");

    Bootstrap::actAsServer();