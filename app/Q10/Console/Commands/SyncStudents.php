<?php

namespace App\Q10\Console\Commands;

use App\Q10\Models\Campus;

use App\Q10\Jobs\CampusEstudianteCursoSync;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;


class SyncStudents extends Command
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
    protected $description = 'Synchronizes the estudiantes from Q10 database with the local database.';

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
            $this->info('Sincronizando campus ' . $campus->Nombre . '...' . PHP_EOL);
            $periodos = $campus->periodos()->where('Estado', true)->get();
            foreach ($periodos as $periodo) {
                $this->info('Sincronizando periodo ' . $periodo->Nombre . '...' . PHP_EOL);
                // Reject the cursos that not have estudiantes
                $jobs = $periodo->cursos->map(function ($curso) use ($campus) {
                    return new CampusEstudianteCursoSync($campus, $curso);
                });
                Bus::batch($jobs)->name($campus->Nombre . ' ' . $periodo->Nombre . ' Sync...')->dispatch();
            }
        }
        $this->info('Jobs creados exitosamente' . PHP_EOL);
        return 0;
    }
}
