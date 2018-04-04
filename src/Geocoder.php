<?php

namespace HussonKevin\Geocoder;

abstract class Geocoder
{
	/**
	 * @var string
	 */
	protected $address;

	/**
	 * @var string
	 */
	protected $country;

	/**
	 * Constructor
	 * 
	 * @param string $address
	 * @param string $country restrict the address to a specific country
	 */
	public function __construct($address, $country)
	{
		$this->address	= $address;
		$this->country	= $country;
	}

	/**
	 * Process query and return a model
	 * 
	 * @param array optional parameters
	 * @return array
	 * @throws \Exception
	 */
	public function process(array $params = [])
	{
		$response	= $this->execute($params);
		if( !is_array($response) ){
			throw new \Exception ('No result');
		}

		return $this->build($response);
	}

	/**
	 * Request API
	 * 
	 * @param array
	 * @return array The decoded json response
	 */
	abstract public function execute(array $params = []);

	/**
	 * Build common response
	 * 
	 * @param array $response The json decode result
	 * @return \HussonKevin\Geocoder\GeocoderModel
	 */
	abstract protected function build(array $response);
}
