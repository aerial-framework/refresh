<?php
    @session_start();

    require_once dirname(__FILE__)."/slim/Slim/Slim.php";
    require_once dirname(__FILE__)."/RESTController.php";
    require_once LIBRARY_PATH."/aerialframework/core/Configuration.php";

    $app = new Slim();

    $controller = RESTController::getInstance();
    $controller->setApp($app);

    // by default, allow any role to access all API operations
    $controller->setAuthCallback(function($roles)
    {
        // if no roles are defined, allow
        if(empty($roles))
            return true;

        // if an operation has an "@authorize debug" annotation,
        // and DEBUG MODE is enabled, allow
        return DEBUG_MODE === true && in_array("debug", $roles);
    });

    // auto-routing must always be declared AFTER authorization callback
    $controller->addAutoRouting(array(new Configuration()));

    // run Slim!
    $app->run();
?>