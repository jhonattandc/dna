<?php

namespace App\Q10\Console\Commands;

use App\Q10\Models\Campus;

use App\Q10\Jobs\CampusEvaluacionesSync;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;


class SyncEvaluations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:evaluationsQ10';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the evaluations from Q10 database with the local database.';

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
     *
     * @return int
     */
    public function handle()
    {
        DB::disableQueryLog();
        $this->info(PHP_EOL . 'Sincronizando los estudiantes de Q10...' . PHP_EOL);
        foreach (Campus::all() as $campus) {
            $jobs = [];
            $this->info('Sincronizando campus ' . $campus->Nombre . '...' . PHP_EOL);
            $periodos = $campus->periodos()->where('Estado', true)->get();
            foreach ($periodos as $periodo) {
                $this->info('Sincronizando periodo ' . $periodo->Nombre . '...' . PHP_EOL);
                $programas = $campus->programas()->where('Estado', true)->get();
                foreach ($programas as $programa){
                    $jobs[] = new CampusEvaluacionesSync($campus, $periodo, $programa);
                }
            }
            Bus::batch($jobs)->name($campus->Nombre .' Evaluations Sync...')->dispatch();
        }
        $this->info('Jobs creados exitosamente' . PHP_EOL);
        return 0;
    }
}
