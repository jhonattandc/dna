<?php

namespace App\Thinkific\Console\Commands;

use App\Q10\Models\Estudiante;
use App\Thinkific\Jobs\CreateStudent;

use Illuminate\Support\Facades\Bus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class SyncStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:studentsTK';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the local database with Thinkific students';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info("Sincronizando estudiantes con Thinkific");
        DB::disableQueryLog();
        // Get students that dosent have a thinkific_user asociated 
        $students = Estudiante::all()->filter(function($student) {
            return $student->thinkific_user == null;
        });
        // Get only 50 students
        $students = $students->take(50);

        $jobs = $students->map(function($student) {
            return new CreateStudent($student);
        });

        Bus::batch($jobs)
            ->name('Create Thinkific Students...')
            ->allowFailures()
            ->dispatch();
            
        $this->info("Creación de trabajos finalizado");
        return 0;
    }
}
