<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\Student;
use App\Services\Q10APIV2;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;


class SyncQ10Students extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:Q10students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes the students from Q10 API';

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
        foreach (Campus::all() as $campus) {
            $this->call("q10:getStudents", ["campus"=>$campus]);
        }
    }
}
