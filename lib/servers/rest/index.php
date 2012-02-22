<?php
    @session_start();

    require_once "slim/Slim/Slim.php";

    $app = new Slim();
    $app->add("Slim_Middleware_ContentTypes");

    $routes = array();

    $app->config('debug', true);

    $app->customRouter(function($callable, $route, $params) use ($routes)
    {
        $app = Slim::getInstance();

        $data = null;

        if(in_array("PUT", $route->getHttpMethods()) || in_array("POST", $route->getHttpMethods()))
            $data = array($app->request()->getBody());
        else
            $data = $params;

        if(!is_callable($callable))
            return false;

        $result = call_user_func_array($callable, $data);

        $app->contentType("application/json");
        $app->response()->header("Access-Control-Allow-Origin", "*");

        if(!$result && $result !== false)
            return true;

        if(is_a($result, "Aerial_Record") || is_a($result, "Doctrine_Collection"))
            $data = json_encode($result->toArray());
        else
            $data = json_encode($result);

        // return gzip-encoded data
        if(substr_count($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") && extension_loaded("zlib"))
        {
            $app->response()->header("Content-Encoding", "gzip");
            $app->response()->header("Vary", "Accept-Encoding");
            $data = gzencode($data, 9, FORCE_GZIP);
        }

        echo $data;

        return true;
    });

    // Custom error handler, only used when debugging is turned OFF
    $app->error(function(Exception $e)
    {
        $app = Slim::getInstance();

        $app->contentType("application/json");
        $app->response()->header("Access-Control-Allow-Origin", "*");

        $app->halt(500, json_encode(array(
                                         "error" => array(
                                             "message" => $e->getMessage(),
                                             "code"    => $e->getCode()
                                         )
                                    )));
    });

    // Custom 404 handler
    $app->notFound(function()
    {
        $app = Slim::getInstance();

        $app->contentType("application/json");
        $app->response()->header("Access-Control-Allow-Origin", "*");

        $app->halt(500, json_encode(array(
                                         "error" => array(
                                             "message" => "'" . $app->request()->getResourceUri() . "' could not be resolved to a valid API call",
                                             "code"    => 500
                                         )
                                    )));
    });

    $app->get("/", function()
    {
        return array("message" => "Hello World");
    });

//    $app->hook("slim.plugin.autoroute.ready", "buildDocumentation");

    require_once "slim-plugins/Plugins/autoroute/AutoRoutePlugin.php";
    require_once "slim-plugins/Plugins/acl/AccessControlPlugin.php";

    $app->registerPlugin("AccessControlPlugin");
    $app->registerPlugin("AutoRoutePlugin", array());


    AccessControlPlugin::authorizationCallback(function($roles)
    {
        // if no role is passed, allow the service request
        if(empty($roles))
            return true;

        $loggedInUser = UserService::getLoggedInUser(false);
        if(empty($loggedInUser) || !$loggedInUser)
            return false;

        // acceptable values: Jobseeker, Recruiter/Employer (account types)
        return in_array($loggedInUser->type, $roles);
    });


    // run Slim!
    $app->run();
?>