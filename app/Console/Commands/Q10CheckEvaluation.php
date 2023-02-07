<?php

namespace App\Console\Commands;

use App\Models\Evaluation;

use App\Events\Q10StudentMiss;
use App\Events\Q10StudentFailed;
use App\Events\Q10StudentPassed;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Q10CheckEvaluation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'q10:checkEvaluation {evaluation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for change in evaluation and dispach the asociate event';

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
        //TODO: Cambiar el argumento que recivo por el json que recibo de Q10
        //TODO: Modificar la logica para buscar en la base de datos la evaluacion
        //      comparar el valor guardado en base de datos y el valor de aprobado/reprobado
        //TODO: Disparar eventos segun sea el caso
        $evaluation = $this->argument('evaluation');
        if (is_int($evaluation) || is_string($evaluation)){
            $evaluation = Evaluation::find($evaluation);
        }

        if ($evaluation->isDirty('Cantidad_inasistencia')){
            if($evaluation->Cantidad_inasistencia > 2){
                Q10StudentFailed::dispatch($evaluation);
            } else if ($evaluation->Cantidad_inasistencia > 0) {
                Q10StudentMiss::dispatch($evaluation);
            }
        }
        return 0;
    }
}
