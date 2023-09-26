<?php

namespace App\Thinkific\Jobs;

use App\Thinkific\Services\ThinkificAPI;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnrollStudent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The course id.
     */
    protected $courseId;

    /**
     * The student id.
     */
    protected $studentId;

    /**
     * Create a new job instance.
     *
     * @param int $courseId
     * @param int $studentId
     * 
     * @return void
     */
    public function __construct($courseId, $studentId)
    {
        $this->courseId = $courseId;
        $this->studentId = $studentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ThinkificAPI $client)
    {
        // TODO: Create migration, model and factory for ThinkificEnrollment
        $enrollments = $client->enroll_user($this->studentId, $this->courseId);
    }
}
