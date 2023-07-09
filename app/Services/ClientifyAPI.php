<?php

namespace App\Services;

use com_exception;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;

class ClientifyAPI extends Client
{
    /**
     * Create a new Clientify API client instance.
     *
     * @param array $config
     * @param str $secret
     *
     * @return void
     */
    public function __construct(array $config = [])
    {
        $config['base_uri'] = env('CLIENTIFY_URL');
        $config['headers'] = [
            'Authorization' => 'Token ' . env('CLIENTIFY_API_KEY')
        ];
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
    public function queryContact($user)
    {
        $options = [
            'query' => [
                'query' => $user->Email
            ]
        ];
        $response = $this->get('contacts', $options);
        $body = json_decode($response->getBody());
        return collect($body->results);
    }

    public function addTagToContact($contact_id, $tag)
    {
        try {
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'name' => $tag
                ]
            ];
            $response = $this->post('contacts/' . $contact_id . '/tags/', $options);
            return $response;
        } catch (com_exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function addPasswordToContact($contact_id, $password)
    {
        try {
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'summary' => $password
                ]
            ];
            $response = $this->put('contacts/' . $contact_id . '/', $options);
            return $response;
        } catch (com_exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
