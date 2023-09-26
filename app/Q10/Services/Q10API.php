<?php

namespace App\Q10\Services;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Q10API extends Client
{
    /**
     * Create a new q10 API client instance.
     *
     * @param array $config
     *
     * @return void
     */
    public function __construct(array $config = [])
    {
        $config['base_uri'] = config('app.integrations.q10.url');
        $headers = array_key_exists('headers', $config) ? $config['headers']: [];
        $config['headers'] = array_merge(
            ['Cache-Control' => 'no-cache'],
            $headers
        );
        parent::__construct($config);
    }

    /**
     * Parse the response body to a collection.
     *
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return \Illuminate\Support\Collection
     */
    public function get_collection($response){
        $body = json_decode($response->getBody(), true);
        return collect($body);
    }


    /**
     * Check if the response is the last page.
     *
     * @param \GuzzleHttp\Psr7\Response $response
     *
     * @return bool
     */
    public function check_end($response){
        if (intval($response->getHeader("x-paging-pagecount")[0]) > 0){
            if(intval($response->getHeader('x-paging-pagenumber')[0]) >= intval($response->getHeader('x-paging-pagecount')[0])){
                return true;
            } else {
                return false;
            }
        } elseif($this->get_collection($response)->count() == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Execute a GET request with the API key to the specified path.
     *
     * @param str $uri
     * @param array $options
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function get_page($uri, array $options = []){
        // Se valida que estén presentes los parametros requeridos para cambiar de paginas
        $options = array_key_exists('query', $options) ? $options : array_merge($options, ['query' => []]);
        if(!array_key_exists('Offset', $options['query'])){
            $options['query']['Offset'] = 1;
        }
        if(!array_key_exists('Limit', $options['query'])){
            $options['query']['Limit'] = 35;
        }
        // Se ejecuta el request de forma recursiva para obtener una collection de todos los items de las paginas
        try {
            $response = $this->get($uri, $options);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 429) {
                Log::debug("Error 429, esperando 60 segundos");
                sleep(60);
                return $this->get_page($uri, $options);
            } else {
                Log::error("Http Code <<{$e->getResponse()->getStatusCode()}>> en la petición a Q10");
                return $e;
            }
        } catch (ServerException $e) {
            Log::error("Http Code <<{$e->getResponse()->getStatusCode()}>> en la petición a Q10");
            return $e;
        }
        return $response;
    }

    /**
     * Execute a GET request with the API key to the specified path.
     *
     * @param str $uri
     * @param array $options
     *
     * @return \Illuminate\Support\Collection
     */
    public function get_paginated($uri, array $options = []) {
        $response = $this->get_page($uri, $options);

        if ($this->check_end($response)){
            return $this->get_collection($response);
        } else {
            $options['query']['Offset'] = $options['query']['Offset'] + 1;
            return $this->get_collection($response)->merge($this->get_paginated($uri, $options));
        }
    }

    /**
     * Execute a GET request with the API key to the specified path and return the page headers.
     * 
     * @param str $uri
     * @param array $options
     * 
     * @return array
     */
    public function get_page_headers($uri, array $options = []){
        $response = $this->get_page($uri, $options);
        return $response->getHeaders();
    }
}
