<?php

	use Behat\Behat\Context\BehatContext,
		Behat\Behat\Exception\PendingException;

	use Guzzle\Service\Client;

	// load Aerial config
	include_once dirname(__FILE__)."/../../../config/config.php";

/**
 * Features context.
 */
class FeatureContextREST extends BehatContext
{
	private $_params		= array();
	private $_client		= null;

	private $_response		= null;

	private $_url;

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param   array   $parameters     context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters)
	{
		if(empty($parameters))
			throw new \Behat\Behat\Exception\Exception("Config file is blank. Check behat.yml", E_USER_ERROR);

		if(!isset($parameters["baseURL"]))
			throw new \Behat\Behat\Exception\Exception("Base URL cannot be found. Check behat.yml", E_USER_ERROR);

		$this->_url = $parameters["baseURL"];
		$this->_client = new Client($this->_url);

		$this->testBaseURL();
	}

	/**
	 * @Given /^that I pass no parameters$/
	 */
	public function thatIPassNoParameters()
	{
		$this->_params = array();
	}

	/**
	 * @When /^I call "([^"]*)"$/
	 */
	public function iCall($url)
	{
		$this->_response = $this->_client->get($this->_url.$url)->send();
	}

	/**
	 * @Then /^the response is JSON$/
	 */
	public function theResponseIsJson()
	{
		if(empty($this->_response))
			throw new \Behat\Behat\Exception\Exception("Response is blank");

		$jsonResponse = $this->_response->getBody(true);
		return json_decode($jsonResponse) && !empty($jsonResponse);
	}

	/**
	 * @Then /^the response status code should be (\d+)$/
	 */
	public function theResponseStatusCodeShouldBe($statusCode)
	{
		return $this->_response->getStatusCode() == $statusCode;
	}

	/**
	 * Validate that the given baseURL is valid
	 *
	 * @throws Behat\Behat\Exception\Exception
	 * @return void
	 */
	private function testBaseURL()
	{
		try
		{
			// test a request to the root
			$this->_client->get("/")->send();
		}
		catch(Exception $e)
		{
			throw new \Behat\Behat\Exception\Exception("Base URL is not valid. Check behat.yml", E_USER_ERROR);
		}
	}
}