<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Q10APIV2 extends Client
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
        $config['base_uri'] = env('Q10_URL');
        $headers = array_key_exists('headers', $config) ? $config['headers']: [];
        $config['headers'] = array_merge(
            ['Cache-Control' => 'no-cache'],
            $headers
        );
        parent::__construct($config);
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
        // Se valida que estén presentes los parametros requeridos para cambiar de paginas
        $options = array_key_exists('query', $options) ? $options : array_merge($options, ['query' => []]);
        if(!array_key_exists('Offset', $options['query'])){
            $options['query']['Offset'] = 1;
        }
        if(!array_key_exists('Limit', $options['query'])){
            $options['query']['Limit'] = 35;
        }
        // Se ejecuta el request de forma recursiva para obtener una collection de todos los items de las paginas
        $response = $this->get($uri, $options);
        $body = json_decode($response->getBody(), true);
        $collection = collect($body);

        if (intval($response->getHeader("x-paging-pagecount")[0]) > 0){
            if(intval($response->getHeader('x-paging-pagenumber')[0]) >= intval($response->getHeader('x-paging-pagecount')[0])){
                Log::debug("pase por la validación del header", [
                    'page_number' => intval($response->getHeader('x-paging-pagenumber')[0]),
                    'page_count' => intval($response->getHeader('x-paging-pagecount')[0])
                ]);
                return $collection;
            }
        } elseif($collection->count() == 0) {
            Log::debug("pase por la validacion de la collection");
            return $collection;
        }
        $options['query']['Offset'] = $options['query']['Offset'] + 1;
        return $collection->merge($this->get_paginated($uri, $options));
    }
}
