<?php

namespace App\Console\Commands;

use App\Models\Campus;

use App\Jobs\ProcessTerms;
use App\Jobs\ProcessCourses;
use App\Jobs\ProcessPrograms;
use App\Jobs\ProcessTimetables;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncQ10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-q10';

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
        logger('Starting sync-q10 command...');
        foreach (Campus::all() as $campus) {
            ProcessTimetables::dispatch($campus)->onQueue($campus->Cola);
            ProcessPrograms::dispatch($campus)->onQueue($campus->Cola);
            ProcessTerms::dispatch($campus)->onQueue($campus->Cola);
            ProcessCourses::dispatch($campus)->onQueue($campus->Cola);
        }
        return 0;
    }
}
