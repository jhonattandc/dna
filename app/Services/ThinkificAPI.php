<?php

namespace App\Services;

use GuzzleHttp\Client;

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
        $config['base_uri'] = env('THINKIFIC_URL');
        $config['headers'] = [
            'X-Auth-API-Key' => env('THINKIFIC_API_KEY'),
            'X-Auth-Subdomain' => env('THINKIFIC_SUBDOMAIN')
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
    public function get_paginated($uri, array $options = []) {
        // Se valida que estén presentes los parametros requeridos para cambiar de paginas
        $options = array_key_exists('query', $options) ? $options : array_merge($options, ['query' => []]);
        if(!array_key_exists('page', $options['query'])){
            $options['query']['page'] = 1;
        }
        if(!array_key_exists('limit', $options['query'])){
            $options['query']['limit'] = 150;
        }
        // Se ejecuta el request de forma recursiva para obtener una collection de todos los items de las paginas
        $response = $this->get($uri, $options);
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
    public function enroll_user($user_id, $course) {
        $enroll = $this->get_paginated('enrollments', [
            'query' => [
                'query[user_id]' => $user_id,
                'query[course_id]' => $course->id
            ]
        ]);

        if ($enroll->count() == 0) {
            $enrollments = $this->post('enrollments', [
                'json' => [
                    'course_id'=>$course->id,
                    'user_id'=>$user_id,
                    'activated_at'=>date('c')
                ]
            ]);
            $enrollment = json_decode($enrollments->getBody(), true);
            Log::debug('User matriculated in course', ['enrollment'=>$enrollment]);
            return $enrollment;
        } else {
            return null;
        }
    }
}
