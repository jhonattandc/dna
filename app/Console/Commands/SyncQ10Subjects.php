<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Subject;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SyncQ10Subjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10subjects {campus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the subjects Q10 database';

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
        $this->info('Obteniendo las asignaturas');

        $campus = $this->argument('campus');
        if (is_int($campus) || is_string($campus)){
            $campus = Campus::find($campus);
        }

        $client = new Q10API('/asignaturas', $campus->Secreto);
        $response = $client->get_paginated();

        $subjets = $response->map(function ($object_json) use ($campus) {
            try {
                $subject = $campus->subjects()->where('Codigo', $object_json['Codigo'])->firstOrFail();
                $subject->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $subject = new Subject($object_json);
                $subject->campus_id = $campus->id;
            }
            $subject->save();
            return $subject;
        });
        return 0;
    }
}
