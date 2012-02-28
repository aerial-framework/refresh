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

            if(empty($app))
                throw new Exception("RESTController could not be initialized with an empty Slim instance");

            // set debug mode to the Aerial DEBUG_MODE setting
            $app->config('debug', Configuration::get("DEBUG_MODE"));

            // add Slim middleware to deserialize HTTP request body data
            $this->addRequestBodyDeserializer();

            // define a custom router function
            $app->customRouter(array($this, "router"));

            // define a custom error-handling function
            $app->error(array($this, "errorHandler"));

            // define a 404 handling function
            $app->notFound(array($this, "notFoundHandler"));

            // add default Aerial operations
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
            require_once dirname(__FILE__) . "/utils/Deserializer.php";
            require_once dirname(__FILE__) . "/utils/Serializer.php";

            $this->initialize();
        }

        public function getApp()
        {
            return $this->_slimInstance;
        }

        private function addRequestBodyDeserializer()
        {
            $app = $this->getApp();

            $app->add("Deserializer");
        }

        public function router($callable, $route, $params)
        {
            $app = $this->getApp();
            $env = $app->environment();

            $data = array();

            if(in_array("PUT", $route->getHttpMethods()) || in_array("POST", $route->getHttpMethods()))
                $data = array($app->request()->getBody());
            else
                $data = $params;

            if(!empty($env["QUERY_STRING"]))
            {
                parse_str($env["QUERY_STRING"], $query);
                $data = array_merge($data, $query);
            }

            if(!is_callable($callable))
                return false;

            $result = call_user_func_array($callable, $data);

            // if there is no response data, return a blank response
            if($result === null && $result !== false)
                return true;

            $data = $this->getSerializedData($result);

            // return gzip-encoded data
            $gzipEnabled = Configuration::get("GZIP_ENABLED");
            if(substr_count($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") && extension_loaded("zlib") && $gzipEnabled)
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
                                                 "code"    => $e->getCode(),
                                                 "file"    => $e->getFile(),
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
                return array("message" => "Hello World from Aerial Framework");
            });

            if(Configuration::get("DEBUG_MODE"))
            {
                // TODO: Add more status info here
                $app->get("/status", function()
                {
                    return array("status" => "operational");
                });

                // add internal Aerial operations
                require_once Configuration::get("LIBRARY_PATH")."/aerialframework/core/CodeGeneration.php";
                $this->addAutoRouting(array(new CodeGeneration()));
            }
        }

        private function getSerializedData($rawResponse)
        {
            $app = $this->getApp();

            $env = $app->environment();
            $acceptableContentTypes = explode(";", $env["ACCEPT"]);

            $contentType = "";

            if(count($acceptableContentTypes) > 1 || empty($acceptableContentTypes))
                $contentType = Configuration::get("DEFAULT_CONTENT_TYPE");
            else
                $contentType = $acceptableContentTypes[0];

            // don't allow */* as the content-type, rather favour the default content-type
            if($contentType == "*/*")
                $contentType = Configuration::get("DEFAULT_CONTENT_TYPE");

            $app->contentType($contentType);

            if(is_a($rawResponse, "Aerial_Record") || is_a($rawResponse, "Doctrine_Collection"))
                $rawResponse = $rawResponse->toArray();

            $data = Serializer::serialize($rawResponse, $contentType);

            return $data;
        }
    }
