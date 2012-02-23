<?php

import('doctrine.Doctrine');
import('aerialframework.doctrine-extensions.Aerial');
import('aerialframework.core.Authentication');
import('aerialframework.core.Configuration');
import('aerialframework.utils.ModelMapper');
import('aerialframework.utils.Date');
import('aerialframework.utils.firephp.fb');

import('aerialframework.encryption.Encrypted');
import('aerialframework.encryption.Encryption');
import('aerialframework.encryption.rc4crypt');

import('aerialframework.exceptions.Aerial_Encryption_Exception');
import('aerialframework.exceptions.Aerial_Exception');

class Bootstrapper
{
	public $conn;
	public $manager;

	private static $instance;

	private function __construct()
	{
		spl_autoload_register(array('Doctrine', 'autoload'));
		spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
		spl_autoload_register(array('Aerial', 'autoload'));

		$this->manager = Doctrine_Manager::getInstance();
			
		$this->manager->registerHydrator(Aerial_Core::HYDRATE_AMF_COLLECTION, Aerial_Core::HYDRATE_AMF_COLLECTION);
		$this->manager->registerHydrator(Aerial_Core::HYDRATE_AMF_ARRAY, Aerial_Core::HYDRATE_AMF_ARRAY);
			
		$this->manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
		$this->manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
		$this->manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);

		$this->setCustomConnections();

		$connectionString =
            DB_ENGINE . "://".
            DB_USERNAME . ":".
            DB_PASSWORD . "@" .
            DB_HOST . ":" .
            DB_PORT . "/" .
            DB_SCHEMA;

		try
		{
			$this->conn = Doctrine_Manager::connection($connectionString, "doctrine");
		}
		catch(Exception $e)
		{
			throw $e;
		}

        if(realpath(PHP_MODELS))
    		Aerial_Core::loadModels(PHP_MODELS);

		Authentication::getInstance();


	}

	public static function setCredentials($username, $password)
	{
		$credentials = new stdClass();
		$credentials->username = $username;
		$credentials->password = $password;

		session_start();
		$_SESSION["credentials"] = $credentials;
	}

	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	/**
	 * Register custom Doctrine connections to catch connection exceptions
	 *
	 * @static
	 * @return void
	 */
	private function setCustomConnections()
	{
		$this->manager->registerConnectionDriver('sqlite', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('sqlite2', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('sqlite3', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('dblib', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('mysql', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('oci8', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('oci', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('pgsql', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('odbc', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('mock', 'Aerial_Connection');
		$this->manager->registerConnectionDriver('oracle', 'Aerial_Connection');
	}
}
?>