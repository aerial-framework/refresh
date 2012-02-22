<?php

    // load the configuration file
    require_once("../config/config.php");

    // load the Bootstrap file
    require_once("../lib/Bootstrap.php");

    if(!realpath(LIBRARY_PATH))
        throw new Exception("Library path not valid: ".LIBRARY_PATH);

    set_include_path(LIBRARY_PATH);

    Bootstrap::actAsServer();