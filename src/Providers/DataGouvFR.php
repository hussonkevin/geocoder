<?php

namespace HussonKevin\Geocoder\Providers;

use HussonKevin\Geocoder\Geocoder;
use HussonKevin\Geocoder\GeocoderModel;
use GuzzleHttp\Client;

/**
 * Implement the adresse.data.gouv.fr API
 * 
 * @see https://adresse.data.gouv.fr/api
 */
class DataGouvFR extends Geocoder
{
	/**
	 * {@inheritdoc}
	 */
	public function execute( array $params = [] )
	{
		$client		= new Client();
		$url		= 'https://api-adresse.data.gouv.fr/search/';
		$res		= $client->request('GET', $url, [
			'query' => array_merge([
				'limit'			=> 1,
				'autocomplete'	=> 0,
				'q'				=> $this->address,
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
