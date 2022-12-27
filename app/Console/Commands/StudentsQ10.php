<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Student;
use App\Services\Q10API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentsQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:studentsQ10';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the students Q10 database';

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
        foreach (Campus::all() as $campus) {
            Log::info("Obteniendo todos los estudiantes", ["Nombre"=>$campus->Nombre]);
            $this->info('Obteniendo todos los estudiantes de '.$campus->Nombre);
            $client = new Q10API('/estudiantes', $campus->Secreto);

            $terms_timetable_programs =  DB::table('terms')->crossJoin('sede_timetables')->crossJoin('programs')
                ->where('terms.campus_id', $campus->id)
                ->where('sede_timetables.campus_id', $campus->id)
                ->where('programs.campus_id', $campus->id)
                ->where('Habilitado', true)
                ->select('terms.id as term_id', 'terms.Consecutivo as term_consecutivo', 'terms.Fecha_inicio', 'terms.Fecha_fin',
                'sede_timetables.id as sede_tm_id', 'sede_timetables.Consecutivo as sede_tm_consecutivo',
                'programs.id as programs_id', 'programs.Codigo as programs_codigo')
                ->get();

            foreach ($terms_timetable_programs as $row) {
                try {
                    $response = $client->get_paginated([
                        'Periodo' => $row->term_consecutivo,
                        'Sede_jornada' => $row->sede_tm_consecutivo,
                        'Programa' => $row->programs_codigo,
                        'Fecha_inicio' => $row->Fecha_inicio,
                        'Fecha_fin' => $row->Fecha_fin
                    ]);
                } catch (\Throwable $th) {
                    continue;
                }

                Log::debug("Numero de estudiantes a procesar ", [
                    'Periodo' => $row->term_consecutivo,
                    'Sede_jornada' => $row->sede_tm_consecutivo,
                    'Programa' => $row->programs_codigo,
                    'Respuesta' => $response->count()
                ]);
            }
        }
        return 0;
    }
}
