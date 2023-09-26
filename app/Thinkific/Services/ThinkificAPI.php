<?php

namespace App\Thinkific\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

use Illuminate\Support\Facades\Log;

class ThinkificAPI extends Client
{
    /**
     * Create a new Thinkific API client instance.
     *
     * @param array $config
     * @param str $secret
     *
     * @return void
     */
    public function __construct(array $config = [])
    {
        $config['base_uri'] = config('app.integrations.thinkific.url');
        $config['headers'] = [
            'X-Auth-API-Key' => config('app.integrations.thinkific.api_key'),
            'X-Auth-Subdomain' => config('app.integrations.thinkific.subdomain'),
        ];
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
        return collect($body->items);
    }

    /**
     * Execute a GET request with the API key to the specified path.
     * 
     * @param str $uri
     * @param array $options
     * 
     * @return \GuzzleHttp\Psr7\Response
     */
    public function get_page($uri, array $options=[]){
        // Se valida que estén presentes los parametros requeridos para cambiar de paginas
        $options = array_key_exists('query', $options) ? $options : array_merge($options, ['query' => []]);
        if(!array_key_exists('page', $options['query'])){
            $options['query']['page'] = 1;
        }
        if(!array_key_exists('limit', $options['query'])){
            $options['query']['limit'] = 150;
        }
        
        // Se realiza el request considerando los posible errores que podrian ocurrir
        try {
            $response = $this->get($uri, $options);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                Log::warning('No se encontraron resultados en Thinkific', ['uri'=>$uri, 'options'=>$options]);
                return $e;
            } elseif ($e->getResponse()->getStatusCode() == 401) {
                Log::error('Error de autenticación en Thinkific', ['uri'=>$uri, 'options'=>$options]);
                return $e;
            } elseif ($e->getResponse()->getStatusCode() == 429) {
                Log::warning('Se alcanzó el límite de consultas a Thinkific', ['uri'=>$uri, 'options'=>$options]);
                sleep(120);
                return $this->get($uri, $options);
            }
            else {
                Log::error('Error en la consulta a Thinkific', ['error'=>$e->getMessage()]);
                return $e;
            }
            return $e;
        } catch (ServerException $e) {
            Log::error('Error en la consulta a Thinkific', ['error'=>$e->getMessage()]);
            return $e;
        }
        return $response;
    }

    /**
     * Execute a GET request recursively with the API key to the specified path.
     *
     * @param str $uri
     * @param array $options
     *
     * @return \Illuminate\Support\Collection
     */
    public function get_paginated($uri, array $options = []) {
        // Se ejecuta el request de forma recursiva para obtener una collection de todos los items de las paginas
        $response = $this->get_page($uri, $options);
        if ($response instanceof Exception) {
            return collect([]);
        }
        $body = json_decode($response->getBody());
        if (is_null($body->meta->pagination->next_page)) {
            return collect($body->items);
        } else {
            $options['query']['page'] = $body->meta->pagination->next_page;
            return collect($body->items)->merge($this->get_paginated($uri, $options));
        }
    }

    /**
     * Execute a POST request to enroll a user in an course
     *
     * @param mixed $user
     * @param mixed $course
     *
     * @return mixed
     */
    public function enroll_user($user_id, $course_id) {
        $enrollments = $this->get_paginated('enrollments', [
            'query' => [
                'query[user_id]' => $user_id,
                'query[course_id]' => $course_id
            ]
        ]);

        if ($enrollments->count() == 0) {
            try {
                $response = $this->post('enrollments', [
                    'json' => [
                        'course_id'=>$course_id,
                        'user_id'=>$user_id,
                        'activated_at'=>date('c')
                    ]
                ]);
                $enrollment = json_decode($response->getBody());
                Log::debug('User matriculated in course', ['enrollment'=>$enrollment]);
            } catch (ClientException $e) {
                if ($e->getResponse()->getStatusCode() == 401) {
                    Log::error('Error de autenticación en Thinkific');
                    return $e;
                } elseif ($e->getResponse()->getStatusCode() == 429) {
                    Log::warning('Se alcanzó el límite de consultas a Thinkific');
                    sleep(120);
                    return $this->enroll_user($user_id, $course_id);
                }
                else {
                    Log::error('Error en la consulta a Thinkific', ['error'=>$e->getMessage()]);
                    return $e;
                }
                return $e;
            } catch (ServerException $e) {
                Log::error('Error en la consulta a Thinkific', ['error'=>$e->getMessage()]);
                return $e;
            }
            return $enrollment;
        } else {
            return $enrollments;
        }
    }

    /**
     * Execute a POST request to create a user
     * 
     * @param array $data
     * 
     * @return mixed
     */
    public function create_user($data){
        try {
            $response = $this->post('users', [
                'json' => $data
            ]);
            return json_decode($response->getBody());
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 401) {
                Log::error('Error de autenticación en Thinkific');
                return $e;
            } elseif ($e->getResponse()->getStatusCode() == 429) {
                Log::warning('Se alcanzó el límite de consultas a Thinkific');
                sleep(120);
                return $this->create_user($data);
            }
            else {
                Log::error('Error en la consulta a Thinkific', ['error'=>$e->getMessage()]);
                return $e;
            }
            return $e;
        } catch (ServerException $e) {
            Log::error('Error en la consulta a Thinkific', ['error'=>$e->getMessage()]);
            return $e;
        }
    }
}
