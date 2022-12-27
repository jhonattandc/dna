<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Services\Q10API;
use App\Models\SedeTimetable;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SedeTimetablesQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:sedeTimetablesQ10 {campus=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the sede_timetable Q10 database';

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
        $this->info('Obteniendo las sede-jornadas');

        $campus_id = $this->argument('campus');
        $campus = Campus::find($campus_id);

        $client = new Q10API('/sedesjornadas', $campus->Secreto);
        $response = $client->get_paginated();

        $sede_timetables = $response->map(function ($object_json) use ($campus) {
            try{
                $sede_timetable = $campus->sede_timetables()->where('Consecutivo', $object_json['Consecutivo'])->firstOrFail();
                $sede_timetable->fill($object_json);
            } catch (ModelNotFoundException $e) {
                $timetable = $campus->timetables()->where('Codigo', $object_json['Codigo_jornada'])->first();;
                if(is_null($timetable)){
                    $this->warn('No se encontró la jornada ' . $object_json['Codigo_jornada']);
                    Log::warning('No se encontró la jornada', ['timetable'=>$object_json['Codigo_jornada']]);
                    return;
                }
                $sede_timetable = new SedeTimetable($object_json);
                $sede_timetable->campus_id = $campus->id;
                $sede_timetable->timetable_id = $timetable->id;
            }
            $sede_timetable->save();
            return $sede_timetable;
        });
        return 0;
    }
}
