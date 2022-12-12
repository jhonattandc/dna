<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Q10API
{
    /**
     * The api path url.
     *
     * @var str
     */
    protected $url;

    /**
     * Array of custom headers.
     *
     * @var array
     */
    protected $headers;

    /**
     * Create a new Q10 API client instance.
     *
     * @param str $url
     * @param str $secret
     *
     * @return void
     */
    public function __construct($url, $secret)
    {
        $this->url = $url;
        $this->headers = ['Api-Key' => $secret];
    }

    /**
     * Execute a GET request with the API key to the specified path.
     *
     * @param array|null $query
     *
     * @return \Illuminate\Support\Collection
     */
    public function get_paginated($query=null) {
        $query = is_null($query)? ['Offset' => 1, 'Limit' => 150] : $query;
        $query = array_key_exists('Offset', $query) ? $query: array_merge($query, ['Offset' => 1]);
        $query = array_key_exists('Limit', $query) ? $query: array_merge($query, ['Limit' => 150]);

        $response = Http::q10($this->headers)->get($this->url, $query);
        $response->throw();

        if($response->header('x-paging-pagenumber') >= $response->header('x-paging-pagecount')){
            return $response->collect();
        } else {
            $query['Offset'] = $query['Offset'] +1;
            return $response->collect()->merge($this->get_paginated($query));
        }
    }
}
