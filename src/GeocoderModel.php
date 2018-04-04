<?php

namespace HussonKevin\Geocoder;

class GeocoderModel
{
	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $street;

	/**
	 * @var string
	 */
	public $zip;

	/**
	 * @var string
	 */
	public $city;

	/**
	 * @var string
	 */
	public $country;

	/**
	 * @var float
	 */
	public $lat;

	/**
	 * @var float
	 */
	public $lng;

	/**
	 * @var float
	 */
	public $score;

	/**
	 * Return formatted type
	 * 
	 * @return string
	 */
	public function getType()
	{
		$value	= $this->type;
		switch($this->type){
			case 'housenumber':
			case 'yes':
			case 'street_address':
				$value	= 'house';
				break;
			case 'street':
			case 'residential':
			case 'route':
				$value	= 'road';
				break;
			case 'municipality':
			case 'city':
			case 'postal_code':
				$value	= 'city';
				break;
		}

		return $value;
	}
}
