<?php

namespace LukasJankowski\Revision\Models;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'new_values' => 'array',
        'old_values' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Get the ones responsible for the revision.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function revisers()
    {
        return $this->morphTo();
    }

    /**
     * Get the revised objects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function revisions()
    {
        return $this->morphTo();
    }

    /**
     * Get the modified fields in an associative array.
     *
     * @return array
     */
    public function getModified()
    {
        $modified = [];
        foreach (array_keys($this->new_values + $this->old_values) as $key) {
            $modified[$key]['new'] = isset($this->new_values[$key]) ? $this->new_values[$key] : null;
            $modified[$key]['old'] = isset($this->old_values[$key]) ? $this->old_values[$key] : null;
        }

        return $modified;
    }
}
