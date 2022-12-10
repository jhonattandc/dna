<?php

namespace App\Console\Commands;

use Exception;

use App\Models\Campus;
use App\Models\Program;
use App\Models\Term;

use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class SyncQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-q10';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the Q10 database with the local database to emit events.';

    /**
     * The base url of q10 API.
     *
     * @var string
     */
    private $base_url = 'https://api.q10.com/v1';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute a get request with the API key.
     *
     * @param string $secret
     *
     * @param string $url
     *
     * @return \Illuminate\Support\Collection
     */
    private function get_request($secret, $url, $query=null){
        $response = Http::withHeaders([
            'Cache-Control' => 'no-cache',
            'Api-Key' => $secret
        ])->get($this->base_url . $url, $query);
        $response->throw();

        return $response->collect();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        info('Starting sync-q10 command...');
        foreach (Campus::all() as $campus) {
            logger('Get campus information:', ['name' => $campus->Nombre]);
        }
        // TODO: Get from DB
        $campus = new Campus;
        $campus->Nombre = 'Medellin';
        $campus->Secreto = 'bc2e35e366c449918512fec22c1c7e28';

        info('Getting program records from Q10');
        try {
            $response = $this->get_request($campus->Secreto, '/programas');
            foreach($response as $program_json) {
                $program = new Program;
                $program->fill($program_json);
                logger('Response from q10 programs', [
                    'response' => $program
                ]);
            }
        } catch (Exception $e) {
            logger($e);
        }

        info('Getting term records from Q10');
        try {
            $response = $this->get_request($campus->Secreto, '/periodos');
            foreach($response as $term_json) {
                $term = new term;
                $term->fill($term_json);
                logger('Response from q10 programs', [
                    'response' => $term
                ]);
            }
        } catch (Exception $e) {
            logger($e);
        }

        return 0;
    }
}
