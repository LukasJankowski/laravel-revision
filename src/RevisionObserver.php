<?php

namespace LukasJankowski\Revision;

use LukasJankowski\Revision\Services\RevisionService;

class RevisionObserver
{
    private $revisionService;

    public function __construct(RevisionService $revisionService)
    {
        $this->revisionService = $revisionService;
    }

    /**
     * Listen to the Model created event.
     *
     * @param $model
     * @return void
     */
    public function created($model)
    {
        $this->revisionService->event(__FUNCTION__, $model);
    }

    /**
     * Listen to the Model updated event.
     *
     * @param $model
     * @return void
     */
    public function updated($model)
    {
        $this->revisionService->event(__FUNCTION__, $model);
    }

    /**
     * Listen to the Model deleted event.
     *
     * @param $model
     * @return void
     */
    public function deleted($model)
    {
        $this->revisionService->event(__FUNCTION__, $model);
    }

    /**
     * Listen to the Model restored event.
     *
     * @param $model
     * @return void
     */
    public function restored($model)
    {
        $this->revisionService->event(__FUNCTION__, $model);
    }
}
