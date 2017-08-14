<?php

namespace LukasJankowski\Revision\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use LukasJankowski\Revision\Models\Revision;

class RevisionService
{
    /**
     * The model, which will be associated with the new revision.
     *
     * @var Model
     */
    private $model;

    /**
     * The revision, which will be created for the model and its changes.
     *
     * @var Revision
     */
    private $revision;

    /**
     * The fields, which will not be logged in the revision.
     *
     * @var array|null
     */
    private $excluded;

    /**
     * The threshold, which will limit the amount of revisions one model can have.
     *
     * @var int|null
     */
    private $threshold;

    /**
     * RevisionService constructor.
     *
     * @param Revision $revision
     */
    public function __construct(Revision $revision)
    {
        $this->revision = $revision;
    }

    /**
     * Get the relevant settings from the model.
     *
     * @param string $event
     * @param Model $model
     */
    public function event($event, Model $model)
    {
        $this->model = $model;
        $this->excluded = $model->getRevisionExclude();
        $this->threshold = $model->getRevisionThreshold();

        $this->cleanThresholdOverflow();

        $this->$event();
    }

    /**
     * The hook for the 'created' event fired by laravel.
     */
    private function created()
    {
        $this->revision->event = __FUNCTION__;
        $this->revision->old_values = [];
        $this->revision->new_values = $this->getAttributes();

        $this->createRevision();
    }

    /**
     * The hook for the 'updated' event fired by laravel.
     */
    private function updated()
    {
        $oldValues = $this->getAttributes(true);
        $newValues = $this->getAttributes();
        $unchangedKeys = array_keys(array_intersect($oldValues, $newValues));

        $this->revision->event = __FUNCTION__;
        $this->revision->old_values = array_except($oldValues, $unchangedKeys);
        $this->revision->new_values = array_except($newValues, $unchangedKeys);

        $this->createRevision();
    }

    /**
     * The hook for the 'deleted' event fired by laravel.
     */
    private function deleted()
    {
        $this->revision->event = __FUNCTION__;
        $this->revision->old_values = $this->getAttributes(true);
        $this->revision->new_values = [];
        $this->revision->deleted_at = Carbon::now();

        $this->createRevision();
    }

    /**
     * The hook for the 'restored' event fired by laravel.
     */
    private function restored()
    {
        $this->revision->event = __FUNCTION__;
        $this->revision->old_values = [];
        $this->revision->new_values = $this->getAttributes(true);

        $this->createRevision();
    }

    /**
     * Clean up the revisions, which exceed beyond the defined threshold.
     */
    private function cleanThresholdOverflow()
    {
        if ($this->threshold) {
            $revisions = Revision::where('revisions_id', $this->model->getKey())
                ->orderBy('id', 'desc')
                ->skip($this->threshold - 1)
                ->limit(PHP_INT_MAX)
                ->select('id')
                ->get()
                ->toArray();
            Revision::destroy(array_flatten($revisions));
        }
    }

    /**
     * Get the filtered attributes from the model.
     *
     * @param bool $original
     * @return array
     */
    private function getAttributes($original = false)
    {
        return array_except(
            $original
                ? $this->model->getOriginal()
                : $this->model->getAttributes(),
            $this->excluded ? $this->excluded : []
        );
    }

    /**
     * Add all remaining data and meta-data to the revision before saving it to the database.
     */
    private function createRevision()
    {
        list($reviserId, $reviserType) = $this->getReviser();
        list($url, $ipAddress, $userAgent) = $this->getMeta();

        $this->revision->revisers_id = $reviserId;
        $this->revision->revisers_type = $reviserType;
        $this->revision->revisions_id = $this->model->getKey();
        $this->revision->revisions_type = get_class($this->model);
        $this->revision->url = str_limit($url, 254);
        $this->revision->ip_address = $ipAddress;
        $this->revision->user_agent = str_limit($userAgent, 254);

        $this->revision->save();
    }

    /**
     * Get the one responsible for the fired event.
     *
     * @return array|null
     */
    private function getReviser()
    {
        foreach (config('auth.guards') as $guardName => $settings) {
            if (auth($guardName)->check()) {
                return [
                    auth($guardName)->user()->getAuthIdentifier(),
                    config('auth.providers.' . $settings['provider'] . '.model')
                ];
            }
        }
    }

    /**
     * Get the meta-data from the environment.
     *
     * @return array
     */
    private function getMeta()
    {
        return [
            app()->runningInConsole() ? 'console' : request()->fullUrl(),
            request()->ip(),
            request()->header('User-Agent')
        ];
    }
}
