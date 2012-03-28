<?php
	class Aerial_Query extends Doctrine_Query
	{
		public function __construct(Doctrine_Connection $connection = null,
									Doctrine_Hydrator_Abstract $hydrator = null)
		{
			parent::__construct($connection, $hydrator);
		}

		public function execute($params = array(), $hydrationMode = null)
		{
			if(empty($hydrationMode))
				$hydrationMode = Configuration::get("DOCTRINE_DEFAULT_HYDRATION");

			return parent::execute($params, $hydrationMode);
		}
	}
