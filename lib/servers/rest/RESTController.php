<?php
    /**
     * Class to control the operation and configuration of Slim Framework
     */
    class RESTController
    {
        private static $instance;

        /**
         * @var Slim
         */
        private $_slimInstance;

        private $_authorizationCallback;


        private function __construct()
        {
        }

        /**
         * Initialize the RESTController
         *
         * @throws Exception
         */
        private function initialize()
        {
            $app = $this->getApp();

            $app->add("Deserializer");

            if(empty($app))
                throw new Exception("RESTController could not be initialized with an empty Slim instance");

            // set debug mode to the Aerial DEBUG_MODE setting
            $app->config('debug', Configuration::get("DEBUG_MODE"));

            // define a custom router function
            $app->customRouter(array($this, "router"));

            // define a custom error-handling function
            $app->error(array($this, "errorHandler"));

            // define a 404 handling function
            $app->notFound(array($this, "notFoundHandler"));

            $this->addDefaultOperations();
        }

        /**
         * @static
         * @return RESTController
         */
        public static function getInstance()
        {
            if(empty(self::$instance))
            {
                $className = __CLASS__;
                self::$instance = new $className;
            }

            return self::$instance;
        }

        public function setApp(Slim $slimInstance)
        {
            $this->_slimInstance = $slimInstance;

            // load in some required files
            require_once dirname(__FILE__)."/utils/Deserializer.php";

            $this->initialize();
        }

        public function getApp()
        {
            return $this->_slimInstance;
        }

        public function router($callable, $route, $params)
        {
            $app = $this->getApp();

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
        }

        public function errorHandler(Exception $e)
        {
            $app = $this->getApp();

            $app->contentType("application/json");
            $app->response()->header("Access-Control-Allow-Origin", "*");

            $app->halt(500, json_encode(array(
                                             "error" => array(
                                                 "message" => $e->getMessage(),
                                                 "code"    => $e->getCode()
                                             )
                                        )));
        }

        public function notFoundHandler()
        {
            $app = $this->getApp();

            $app->contentType("application/json");
            $app->response()->header("Access-Control-Allow-Origin", "*");

            $app->halt(500, json_encode(array(
                                             "error" => array(
                                                 "message" => "'" . $app->request()->getResourceUri() . "' could not be resolved to a valid API call",
                                                 "code"    => 500
                                             )
                                        )));
        }

        public function addAutoRouting(array $classes)
        {
            $app = $this->getApp();

            require_once dirname(__FILE__) . "/slim-plugins/Plugins/autoroute/AutoRoutePlugin.php";
            $app->registerPlugin("AutoRoutePlugin", $classes);
        }

        public function setAuthCallback($authorizationCallback)
        {
            $app = $this->getApp();

            require_once dirname(__FILE__) . "/slim-plugins/Plugins/acl/AccessControlPlugin.php";
            $app->registerPlugin("AccessControlPlugin");

            if(!is_callable($authorizationCallback))
            {
                $this->_authorizationCallback = null;
                AccessControlPlugin::authorizationCallback(null);

                throw new Exception("Function used for setAuthCallback is not callable.");
            }

            $this->_authorizationCallback = $authorizationCallback;

            AccessControlPlugin::authorizationCallback($this->getAuthCallback());
        }

        public function getAuthCallback()
        {
            return $this->_authorizationCallback;
        }

        private function addDefaultOperations()
        {
            $app = $this->getApp();

            $app->get("/", function()
            {
                return array("message" => "Hello World");
            });

            if(DEBUG_MODE)
            {
                // TODO: Add more status info here
                $app->get("/status", function()
                {
                    return array("status" => "operational");
                });
            }
        }
    }
