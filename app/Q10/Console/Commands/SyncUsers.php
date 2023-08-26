<?php

namespace App\Q10\Console\Commands;

use App\Q10\Models\Campus;

use App\Q10\Jobs\CampusUsariosSync;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class SyncUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:usersQ10';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the users from Q10 database with the local database.';

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
        $this->info(PHP_EOL . 'Sincronizando los usuarios de Q10...' . PHP_EOL);
        foreach (Campus::all() as $campus) {
            $this->info('Sincronizando campus ' . $campus->Nombre . '...' . PHP_EOL);
            Bus::batch([
                new CampusUsariosSync($campus),
            ])->name($campus->Nombre . ' User Sync...')->dispatch();
        }
        $this->info('Jobs creados exitosamente' . PHP_EOL);
        return 0;
    }
}
