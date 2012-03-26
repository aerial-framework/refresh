<?php

    // load the configuration file
    require_once("../config/config.php");

    // load the Bootstrap file
    require_once(Configuration::get("LIBRARY_PATH")."/Bootstrap.php");

    Bootstrap::actAsServer();