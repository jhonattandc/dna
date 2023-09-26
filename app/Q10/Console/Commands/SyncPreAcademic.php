<?php

namespace App\Q10\Console\Commands;

use App\Q10\Models\Campus;

use App\Q10\Jobs\CampusAsignaturasSync;
use App\Q10\Jobs\CampusJornadasSync;
use App\Q10\Jobs\CampusNivelesSync;
use App\Q10\Jobs\CampusPeriodosSync;
use App\Q10\Jobs\CampusProgramasSync;
use App\Q10\Jobs\CampusSedesSync;
use App\Q10\Jobs\CampusJornadaSedeSync;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class SyncPreAcademic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:preAcademicQ10';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the basic academic info from Q10 database with the local database.';

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
        $this->info(PHP_EOL . 'Sincronizando elementos academicos bases de las relaciones necesarios para los usuarios de Q10...' . PHP_EOL);
        foreach (Campus::all() as $campus) {
            $this->info('Sincronizando campus ' . $campus->Nombre . '...' . PHP_EOL);
            Bus::batch([
                new CampusAsignaturasSync($campus),
                new CampusJornadasSync($campus),
                new CampusNivelesSync($campus),
                new CampusPeriodosSync($campus),
                new CampusProgramasSync($campus),
                new CampusSedesSync($campus),
                new CampusJornadaSedeSync($campus),
            ])->name($campus->Nombre . ' Basic Academic Sync...')->dispatch();
        }
        $this->info('Jobs creados exitosamente' . PHP_EOL);
        return 0;
    }
}
