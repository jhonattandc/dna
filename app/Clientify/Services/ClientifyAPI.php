<?php

namespace App\Clientify\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

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
     * Query a contact by email.
     *
     * @param str $email
     *
     * @return \Illuminate\Support\Collection
     */
    public function get_contacts($email)
    {
        try {
            $options = [
                'query' => [
                    'query' => $email
                ]
            ];
            $response = $this->get('contacts', $options);
            $body = json_decode($response->getBody());
            return collect($body->results);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 429) {
                Log::warning('Se alcanzó el límite de consultas a Clientify', ['uri'=>'contacts', 'options'=>$options]);
                sleep(60);
                $this->get_contacts($email);
            } else {
                Log::error('Error en la consulta a Clientify', ['error'=>$e->getMessage()]);
                return $e;
            }
        } catch (ServerException $e) {
            Log::error('Error en la consulta a Clientify', ['error'=>$e->getMessage()]);
            return $e;
        }
    }

    public function add_tag_contact($contact_id, $tag)
    {
        try {
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'name' => is_null($tag) ? '': $tag
                ]
            ];
            $response = $this->post('contacts/' . $contact_id . '/tags/', $options);
            return $response;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 429) {
                Log::warning('Se alcanzó el límite de consultas a Clientify', ['uri'=>'contacts/' . $contact_id . '/tags/', 'options'=>$options]);
                sleep(60);
                $this->add_tag_contact($contact_id, $tag);
            } else {
                Log::error('Error en la consulta a Clientify', ['error'=>$e->getMessage()]);
                return $e;
            }
        } catch (ServerException $e) {
            Log::error('Error en la consulta a Clientify', ['error'=>$e->getMessage()]);
            return $e;
        }
    }

    public function add_sumary_contact($contact_id, $sumary)
    {
        try {
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'summary' => is_null($sumary) ? '': $sumary
                ]
            ];
            $response = $this->put('contacts/' . $contact_id . '/', $options);
            return $response;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 429) {
                Log::warning('Se alcanzó el límite de consultas a Clientify', ['uri'=>'contacts/' . $contact_id . '/', 'options'=>$options]);
                sleep(60);
                $this->add_sumary_contact($contact_id, $sumary);
            } else {
                Log::error('Error en la consulta a Clientify', ['error'=>$e->getMessage()]);
                return $e;
            }
        } catch (ServerException $e) {
            Log::error('Error en la consulta a Clientify', ['error'=>$e->getMessage()]);
            return $e;
        }
    }
}
