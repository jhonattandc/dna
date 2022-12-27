<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Timetable;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TimetablesQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:timetablesQ10 {campus=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the timetables Q10 database';

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
        $this->info('Obteniendo las jornadas');

        $campus_id = $this->argument('campus');
        $campus = Campus::find($campus_id);

        $client = new Q10API('/jornadas', $campus->Secreto);
        $response = $client->get_paginated();

        $timetables = $response->map(function ($object_json) use ($campus) {
            try{
                $timetable = $campus->timetables()->where('Codigo', $object_json['Codigo'])->firstOrFail();
                $timetable->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $timetable = new Timetable($object_json);
                $timetable->campus_id = $campus->id;
            }
            $timetable->save();
            return $timetable;
        });

        return 0;
    }
}
