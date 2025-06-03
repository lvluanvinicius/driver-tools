<?php
namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DestroyFilesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $filePath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Storage::disk('driver_tool')->delete($this->filePath);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
