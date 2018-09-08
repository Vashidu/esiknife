<?php

namespace ESIK\Jobs\Members;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Log;
use ESIK\Models\Member;
use ESIK\Traits\Trackable;
use ESIK\Http\Controllers\DataController;

class GetMemberContracts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public $id, $dataCont;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $id)
    {
        $this->id = $id;
        $this->dataCont = new DataController;
        $this->prepareStatus();
        $this->setInput(['id' => $id]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $member = Member::findOrFail($this->id);
        $getMemberContracts = $this->dataCont->getMemberContracts($member);
        $status = $getMemberContracts->status;
        $payload = $getMemberContracts->payload;
        if (!$status) {
            Log::alert($payload->message);
        }
    }
}
