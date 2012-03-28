<?php
	class Bootstrap
	{
		public function __construct()
		{
			if (!function_exists("is_undefined"))
			{
				function is_undefined($obj)
				{
					return is_object($obj) ? get_class($obj) == "Amfphp_Core_Amf_Types_Undefined" : false;
				}
			}
		}

		public static function actAsServer($override = null)
		{
			// instantiate the Aerial bootstrapper
			Bootstrapper::getInstance();

			$libraryPath = Configuration::get("LIBRARY_PATH");
			if (empty($libraryPath) || !realpath($libraryPath))
				throw new Exception("LIBRARY_PATH is invalid.");

			if (empty($override))
				$contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : null;
			else
				$contentType = $override;

			switch ($contentType)
			{
				default:
					include_once($libraryPath . "/servers/rest/index.php");
					break;
				case "application/x-amf":
					include_once($libraryPath . "/servers/amfphp2/index.php");
					break;
			}
		}

		public static function actAsLibrary()
		{
			// instantiate the Aerial bootstrapper
			Bootstrapper::getInstance();
		}
	}
