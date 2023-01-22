<?php

namespace App\Console\Commands;

use App\Models\Term;
use App\Models\Campus;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SyncQ10Terms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10terms {campus}';

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

        $campus = $this->argument('campus');
        if (is_int($campus) || is_string($campus)){
            $campus = Campus::find($campus);
        }

        $client = new Q10API('/periodos', $campus->Secreto);
        $response = $client->get_paginated();

        $terms = $response->map(function ($object_json) use ($campus){
            try{
                $term = $campus->terms()->where('Consecutivo', $object_json['Consecutivo'])->firstOrFail();
                $term->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $term = new Term($object_json);
                $term->campus_id = $campus->id;
                Log::warning("Nuevo periodo de Q10 sincronizado, es necesario marcarlo habilitado para sincronizar todos los elementos asociados a el", ['periodo'=>$term->id]);
            }
            $term->save();
            return $term;
        });
        return 0;
    }
}
