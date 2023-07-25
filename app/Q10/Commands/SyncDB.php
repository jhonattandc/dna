<?php

namespace App\Q10\Commands;

use App\Q10\Models\Campus;

use App\Q10\Jobs\CampusRolesSync;
use App\Q10\Jobs\CampusPerfilesSync;
use App\Q10\Jobs\CampusTiposIdSync;

use App\Q10\Jobs\CampusUsariosSync;
use App\Q10\Jobs\CampusAdministrativosSync;
use App\Q10\Jobs\CampusDocentesSync;
use App\Q10\Jobs\CampusJornadaSedeSync;
use App\Q10\Jobs\CampusPeriodosSync;
use App\Q10\Jobs\CampusProgramasSync;
use App\Q10\Jobs\CampusSedesSync;
use App\Q10\Jobs\CampusJornadasSync;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;

class SyncDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10DB';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the Q10 database with the local database to emit events.';

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
        DB::disableQueryLog();
        $this->info(PHP_EOL . 'Sincronizando base de datos de Q10...' . PHP_EOL);
        foreach (Campus::all() as $campus) {
            Bus::chain([
                // new CampusRolesSync($campus),
                // new CampusPerfilesSync($campus),
                // new CampusTiposIdSync($campus),
                // new CampusUsariosSync($campus),
                // new CampusAdministrativosSync($campus),
                // new CampusDocentesSync($campus),
                // new CampusPeriodosSync($campus),
                // new CampusProgramasSync($campus),
                // new CampusSedesSync($campus),
                // new CampusJornadasSync($campus),
                new CampusJornadaSedeSync($campus),
            ])->dispatch();
        }
        $this->info('Jobs creados exitosamente' . PHP_EOL);
        return 0;
    }
}
