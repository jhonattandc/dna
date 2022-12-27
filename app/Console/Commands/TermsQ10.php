<?php

namespace App\Console\Commands;

use App\Models\Term;
use App\Models\Campus;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TermsQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:termsQ10 {campus=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the terms Q10 database';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Obteniendo los periodos');

        $campus_id = $this->argument('campus');
        $campus = Campus::find($campus_id);

        $client = new Q10API('/periodos', $campus->Secreto);
        $response = $client->get_paginated();

        $terms = $response->map(function ($object_json) use ($campus){
            try{
                $term = $campus->terms()->where('Consecutivo', $object_json['Consecutivo'])->firstOrFail();
                $term->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $term = new Term($object_json);
                $term->campus_id = $campus->id;
            }
            $term->save();
            return $term;
        });
        return 0;
    }
}
