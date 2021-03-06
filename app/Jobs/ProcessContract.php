<?php

namespace ESIK\Jobs;

use Bus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use ESIK\Models\Member;
use ESIK\Traits\Trackable;
use ESIK\Http\Controllers\DataController;
use ESIK\Models\ESI\{Character, Contract, Corporation, Alliance, Station, Structure};
use ESIK\Jobs\ESI\{GetCharacter, GetCorporation, GetAlliance, GetStation, GetStructure, GetContractItems};

use Illuminate\Support\Collection;

class ProcessContract implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public $memberId, $contract, $dataCont;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $memberId, string $contract)
    {
        $this->memberId = $memberId;
        $this->contract = $contract;
        $this->dataCont = new DataController;
        $this->prepareStatus();
        $this->setInput(['memberId' => $memberId, 'contract' => $contract]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $member = Member::findOrFail($this->memberId);
        $contract = collect(json_decode($this->contract, true));
        $dbContract = Contract::findOrFail($contract->get('contract_id'));
        $now = now(); $x = 0; $dispatchedJobs = collect();
        $entities = $contract->filter(function ($entity, $key) {
            if (in_array($key, ['issuer_id', 'issuer_corporation_id', 'assignee_id', 'acceptor_id']) && $entity != 0) {
                return true;
            }
        });
        $entities = $entities->unique()->values();
        $postEntities = $this->dataCont->postUniverseNames($entities);
        $pEStatus = $postEntities->status;
        $pEPayload = $postEntities->payload;
        if ($pEStatus) {
            $pEResponse = collect($pEPayload->response)->recursive()->keyBy('id');
            if ($pEResponse->has($dbContract->assignee_id)) {
                $dbContract->assignee_type = $pEResponse->get($dbContract->assignee_id)->get('category');
            }
            if ($pEResponse->has($dbContract->acceptor_id)) {
                $dbContract->acceptor_type = $pEResponse->get($dbContract->acceptor_id)->get('category');
            }

            $characterIds = $pEResponse->where('category', 'character')->pluck('id');
            $corporationIds = $pEResponse->where('category', 'corporation')->pluck('id');
            $allianceIds = $pEResponse->where('category', 'alliance')->pluck('id');

            $knownCharacters = Character::whereIn('id', $characterIds->toArray())->get()->keyBy('id');
            $knownCorporations = Corporation::whereIn('id', $corporationIds->toArray())->get()->keyBy('id');
            $knownAlliances = Alliance::whereIn('id', $allianceIds->toArray())->get()->keyBy('id');
            $x = 0;
            $characterIds->diff($knownCharacters->keys())->each(function ($characterId) use (&$now, &$x, $dispatchedJobs) {
                $class = \ESIK\Jobs\ESI\GetCharacter::class;
                $params = collect(['id' => $characterId]);
                $jobId = $this->dataCont->dispatchJob($class, $params, $now);
                $jobId->get('dispatched') ? $dispatchedJobs->push($jobId->get('job')) : "";
                if ($x%10==0) {
                    $now->addSecond();
                }
                $x++;
            });
            $x = 0;
            $corporationIds->diff($knownCorporations->keys())->each(function ($corporationId) use (&$now, &$x, $dispatchedJobs) {
                $class = \ESIK\Jobs\ESI\GetCorporation::class;
                $params = collect(['id' => $corporationId]);
                $jobId = $this->dataCont->dispatchJob($class, $params, $now);
                $jobId->get('dispatched') ? $dispatchedJobs->push($jobId->get('job')) : "";
                if ($x%10==0) {
                    $now->addSecond();
                }
                $x++;
            });
            $x = 0;
            $allianceIds->diff($knownAlliances->keys())->each(function ($allianceId) use (&$now, &$x, $dispatchedJobs) {
                $class = \ESIK\Jobs\ESI\GetAlliance::class;
                $params = collect(['id' => $allianceId]);
                $jobId = $this->dataCont->dispatchJob($class, $params, $now);
                $jobId->get('dispatched') ? $dispatchedJobs->push($jobId->get('job')) : "";
                if ($x%10==0) {
                    $now->addSecond();
                }
                $x++;
            });
        }
        $structureIds = collect(); $stationIds = collect();
        $locations = $contract->each(function ($entity, $key) use (&$structureIds, &$stationIds) {
            if (in_array($key, ['start_location_id', 'end_location_id']) && $entity != 0) {
                if ($entity >= 1000000000000) {
                    $structureIds->push($entity);
                } else {
                    $stationIds->push($entity);
                }
            }
        });
        $structureIds = $structureIds->unique()->values();
        $knownStructures = Structure::whereIn('id', $structureIds->toArray())->get()->keyBy('id');
        $x = 0;
        $structureIds->diff($knownStructures->keys())->each(function ($structureId) use ($member, &$now, &$x, $dispatchedJobs) {
            if ($member->scopes->contains(config('services.eve.scopes.readUniverseStructures'))) {
                $class = \ESIK\Jobs\ESI\GetStructure::class;
                $params = collect(['memberId' => $member->id, 'id' => $structureId]);
                $jobId = $this->dataCont->dispatchJob($class, $params, $now);
                $jobId->get('dispatched') ? $dispatchedJobs->push($jobId->get('job')) : "";
                if ($x%10==0) {
                    $now->addSecond();
                }
                $x++;
            }
        });
        $stationIds = $stationIds->unique()->values();
        $knownStations = Station::whereIn('id', $stationIds->toArray())->get()->keyBy('id');
        $x = 0;
        $stationIds->diff($knownStations->keys())->each(function ($stationId) use (&$now, &$x, $dispatchedJobs) {
            $class = \ESIK\Jobs\ESI\GetStation::class;
            $params = collect(['id' => $stationId]);
            $jobId = $this->dataCont->dispatchJob($class, $params, $now);
            $jobId->get('dispatched') ? $dispatchedJobs->push($jobId->get('job')) : "";
            if ($x%10==0) {
                $now->addSecond();
            }
            $x++;
        });
        $dbContract->save();

        $class = \ESIK\Jobs\ESI\GetContractItems::class;
        $params = collect(['memberId' => $this->memberId, 'contractId' => $contract->get('contract_id')]);
        $jobId = $this->dataCont->dispatchJob($class, $params, $now);
        $jobId->get('dispatched') ? $dispatchedJobs->push($jobId->get('job')) : "";
        $member->jobs()->attach($dispatchedJobs->toArray());
        return true;
    }
}
