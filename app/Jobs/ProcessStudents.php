<?php

namespace App\Jobs;

use App\Models\Campus;
use App\Models\Student;
use App\Services\Q10API;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProcessStudents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The campus instance.
     *
     * @var \App\Models\Campus
     */
    protected $campus;

    /**
     * The http client instance.
     *
     * @var \App\Services\Q10API
     */
    protected $client;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Campus $campus
     *
     * @return void
     */
    public function __construct(Campus $campus)
    {
        $this->campus = $campus;
        $this->client = new Q10API('/programas', $campus->Secreto);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
