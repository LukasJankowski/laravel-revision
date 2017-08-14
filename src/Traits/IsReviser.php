<?php

namespace LukasJankowski\Revision\Traits;

use LukasJankowski\Revision\Models\Revision;

trait IsReviser
{
    /**
     * Get the revisions made by this model.
     *
     * @return mixed
     */
    public function revised()
    {
        return $this->morphMany(Revision::class, 'revisers');
    }
}
