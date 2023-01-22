<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Student;
use App\Services\Q10APIV2;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class SyncQ10Students extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the students from Q10 API';

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
            Log::debug("Obteniendo todos los estudiantes", ["Nombre"=>$campus->Nombre]);
            $this->info('Obteniendo todos los estudiantes de '.$campus->Nombre);
            $client = new Q10APIV2([
                'headers' => ['Api-Key'=>$campus->Secreto]
            ]);
            $users = $client->get_paginated('usuarios');

            $bar = $this->output->createProgressBar(count($users));
            $bar->start();
            foreach ($users as $user) {
                # Se verifica que el usuario es un estudiante
                $is_student = false;
                foreach ($user['Roles'] as $role) {
                    if (intval($role['Codigo']) == 1) {
                        $is_student = true;
                    }
                }
                if(!$is_student) {
                    $bar->advance();
                    continue;
                }

                # Se busca en la base de datos local, si no se encuentra se guardan sus datos
                try {
                    $student = Student::where('Codigo_estudiante', $user['Codigo_persona'])->firstOrFail();
                } catch (ModelNotFoundException $e) {
                    $response = $client->get('estudiantes/'.$user['Codigo_persona']);
                    $student_j = json_decode($response->getBody(), true);
                    if($response->getStatusCode() > 200 && $response->getStatusCode() < 300){
                        $student = Student::create($student_j);
                    } else {
                        Log::warning("No se pudo obtener el detalle del estudiante", ["code"=>$response->getStatusCode(), "body"=>$response->getBody()]);
                        $this->warn("No se pudo obtener el detalle del estudiante " . $user['Codigo_persona']);
                        $bar->advance();
                        continue;
                    }
                }

                # Se verifica que este registrado en thinkific y se matricula en onboarding
                $this->call('thinkific:enrollQ10default', ['student'=>$student]);
                sleep(0.05);
                $bar->advance();
            }
            $bar->finish();
            $this->info(" ¡Estudiantes sincronizados de ".$campus->Nombre."!");
            sleep(0.1);
        }
    }
}
