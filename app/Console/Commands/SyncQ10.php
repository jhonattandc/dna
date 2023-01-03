<?php

namespace App\Console\Commands;

use App\Models\Campus;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10';

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
        foreach (Campus::all() as $campus) {
            $this->info('Sincronizando base de datos de Q10 de ' . $campus->Nombre);
            Log::debug("Sincronizando base de datos de Q10", ["Nombre"=>$campus->Nombre]);

            $this->call('sync:timetablesQ10', ['campus' => $campus->id]);
            $this->call('sync:programsQ10', ['campus' => $campus->id]);
            $this->call('sync:termsQ10', ['campus' => $campus->id]);
            $this->call('sync:subjectsQ10', ['campus' => $campus->id]);
            $this->call('sync:sedeTimetablesQ10', ['campus' => $campus->id]);
            $this->call('sync:coursesQ10', ['campus' => $campus->id]);
        }
        return 0;
    }
}
