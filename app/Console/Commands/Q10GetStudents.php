<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Student;
use App\Models\Tkcourse;
use App\Services\ThinkificAPI;
use App\Services\Q10APIV2;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Q10GetStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'q10:getStudents {campus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all students of a campus';

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
    public function handle(ThinkificAPI $tk_client)
    {
        $campus = $this->argument('campus');
        if (is_int($campus) || is_string($campus)){
            $campus = Campus::find($campus);
        }
        Log::debug("Obteniendo todos los estudiantes", ["Nombre"=>$campus->Nombre]);
        $this->info('Obteniendo todos los estudiantes de '.$campus->Nombre);
        $client = new Q10APIV2([
            'headers' => ['Api-Key'=>$campus->Secreto]
        ]);
        $users = $client->get_paginated('usuarios');

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();
        $count = 0;
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
            $count = $count + 1;

            # Se busca en la base de datos local, si no se encuentra se guardan sus datos
            try {
                $student = Student::where('Codigo_estudiante', $user['Codigo_persona'])->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $this->info("Obteniendo datos de estudiante " . $user['Codigo_persona']);
                $response = $client->get('estudiantes/'.$user['Codigo_persona']);
                $student_j = json_decode($response->getBody(), true);
                if($response->getStatusCode() > 300){
                    Log::warning("No se pudo obtener el detalle del estudiante", ["code"=>$response->getStatusCode(), "body"=>$response->getBody()]);
                    $this->warn("No se pudo obtener el detalle del estudiante " . $user['Codigo_persona']);
                    $bar->advance();
                    continue;
                }
                $student = new Student($student_j);
                $student->save();
            }
            sleep(0.15);
        }
        $bar->finish();
        $this->info(" ¡Estudiantes sincronizados de ".$campus->Nombre."!");
        $this->info("Se encontraron ". $count ." estudiantes");
        return 0;
    }
}
