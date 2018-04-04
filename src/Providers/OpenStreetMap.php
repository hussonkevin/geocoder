<?php

namespace App\Libraries\Geocoder\Providers;

use App\Libraries\Geocoder\Geocoder;
use App\Libraries\Geocoder\GeocoderModel;
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
		return $model;
		$model->street	= $response['features'][0]['properties']['name'];
		$model->zip		= $response['features'][0]['properties']['postcode'];
		$model->city	= $response['features'][0]['properties']['city'];
		$model->country	= 'FR';
		$model->type	= $response['features'][0]['properties']['type'];
		$model->score	= $response['features'][0]['properties']['score'];
		if( strtolower($response['features'][0]['geometry']['type']) === 'point' ){
			$model->lng	= $response['features'][0]['geometry']['coordinates'][0];
			$model->lat	= $response['features'][0]['geometry']['coordinates'][1];
		}

		return $model;
	}
}
