<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Program;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProgramsQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:programsQ10 {campus=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the program Q10 database';

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
        $this->info('Obteniendo los programas');

        $campus_id = $this->argument('campus');
        $campus = Campus::find($campus_id);

        $client = new Q10API('/programas', $campus->Secreto);
        $response = $client->get_paginated();

        $programs = $response->map(function ($object_json) use ($campus){
            try{
                $program = $campus->programs()->where('Codigo', $object_json['Codigo'])->firstOrFail();
                $program->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $program = new Program($object_json);
                $program->campus_id = $campus->id;
            }
            $program->save();
            return $program;
        });

        return 0;
    }
}
