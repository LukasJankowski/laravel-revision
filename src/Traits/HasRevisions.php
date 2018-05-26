<?php

namespace LukasJankowski\Revision\Traits;

use LukasJankowski\Revision\Models\Revision;
use LukasJankowski\Revision\RevisionObserver;

trait HasRevisions
{
    /**
     * Boot the trait and register the observer.
     */
    public static function bootHasRevisions()
    {
        static::observe(RevisionObserver::class);
    }

    /**
     * Get the revisions this model has.
     *
     * @return mixed
     */
    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisions');
    }

    /**
     * Get the fields to exclude from the revisions.
     *
     * @return mixed
     */
    public function getRevisionExclude()
    {
        return isset($this->revisionExclude)
            ? $this->revisionExclude
            : config('revisions.default_exclude', []);
    }

    /**
     * Get the threshold, which the revisions will be limited to.
     *
     * @return mixed
     */
    public function getRevisionThreshold()
    {
        return isset($this->revisionThreshold)
            ? $this->revisionThreshold
            : config('revisions.default_threshold');
    }
}
