<?php

namespace HussonKevin\Geocoder\Providers;

use HussonKevin\Geocoder\Geocoder;
use HussonKevin\Geocoder\GeocoderModel;
use GuzzleHttp\Client;

/**
 * Implement the OpenStreetMap API
 * 
 * @see https://wiki.openstreetmap.org/wiki/Nominatim
 */
class OpenStreetMap extends Geocoder
{
	/**
	 * {@inheritdoc}
	 */
	public function execute( array $params = [] )
	{
		$client		= new Client();
		$url		= 'https://nominatim.openstreetmap.org/search';
		$res		= $client->request('GET', $url, [
			'query' => array_merge([
				'format'			=> 'json',
				'addressdetails'	=> '1',
				'bounded'			=> '0',
				'polygon'			=> '0',
				'limit'				=> '1',
				'q'					=> $this->address,
			], $params)
		]);

		return json_decode($res->getBody(), true);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function build(array $response)
	{
		$model			= new GeocoderModel();
		if( !isset($response[0]) ){
			return $model;
		}
		$street			= $response[0]['address']['road'] ?? null;
		$street_number	= $response[0]['address']['house_number'] ?? null;
		$model->street	= ($street_number ? $street_number.' ' : '') . $street;
		$model->zip		= $response[0]['address']['postcode'] ?? null;
		$model->city	= $response[0]['address']['city'] ?? $response[0]['address']['town'] ?? null;
		$model->country	= strtoupper($response[0]['address']['country_code']) ?? null;
		$model->lat		= $response[0]['lat'] ?? null;
		$model->lng		= $response[0]['lon'] ?? null;
		$model->type	= $response[0]['type'] ?? null;
		$model->score	= $response[0]['importance'] ?? null;

		return $model;
	}
}
