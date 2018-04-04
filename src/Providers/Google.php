<?php

namespace HussonKevin\Geocoder\Providers;

use HussonKevin\Geocoder\Geocoder;
use HussonKevin\Geocoder\GeocoderModel;
use GuzzleHttp\Client;

/**
 * Implement the Google Geocode API
 * 
 * @see https://developers.google.com/maps/documentation/geocoding/intro?hl=fr
 * @see https://developers.google.com/maps/documentation/geocoding/usage-limits?hl=fr
 */
class Google extends Geocoder
{
	/**
	 * {@inheritdoc}
	 */
	public function execute( array $params = [] )
	{
		$client		= new Client();
		$url		= 'https://maps.googleapis.com/maps/api/geocode/json';
		$res		= $client->request('GET', $url, [
			'query' => array_merge([
				'components'	=> 'country:' . $this->country,
				'address'		=> $this->address,
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
		if( !isset( $response['results'][0] )){
			return $model;
		}
		if( isset( $response['results'][0]['address_components'] )){
			$street			= null;
			$street_number	= null;
			foreach( $response['results'][0]['address_components'] as $component ){
				foreach($component['types'] as $type){
					switch( $type ){
						case 'street_number':
							$street_number	= $component['short_name'];
							break;
						case 'route':
							$street			= $component['short_name'];
							break;
						case 'postal_code':
							$model->zip		= $component['short_name'];
							break;
						case 'locality':
							$model->city	= $component['short_name'];
							break;
						case 'country':
							$model->country	= $component['short_name'];
							break;
					}
				}
			}
			$model->street	= ($street_number ? $street_number.' ' : '') . $street;
			$model->type	= $response['results'][0]['types'][0] ?? null;
			$model->lng		= $response['results'][0]['geometry']['location']['lng'] ?? null;
			$model->lat		= $response['results'][0]['geometry']['location']['lat'] ?? null;
		}

		return $model;
	}
}
