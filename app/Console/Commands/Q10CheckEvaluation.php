<?php

namespace App\Console\Commands;

use App\Models\Evaluation;
use App\Events\Q10StudentFailed;
use App\Events\Q10StudentAbsented;

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
        $evaluation = $this->argument('evaluation');
        if (is_int($evaluation)){
            $evaluation = Evaluation::find($evaluation);
        }
        if ($evaluation->isDirty('Cantidad_inasistencia')){
            if($evaluation->Cantidad_inasistencia > 2){
                Q10StudentFailed::dispatch($evaluation);
            } else if ($evaluation->Cantidad_inasistencia > 0) {
                Q10StudentAbsented::dispatch($evaluation);
            }
        }
        return 0;
    }
}
