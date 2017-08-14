<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Revision defaults
    |--------------------------------------------------------------------------
    |
    | This options control the behaviour of the revision package.
    |
    */

    /**
     * The default table name for the revisions.
     */
    'table_name' => 'revisions',

    /**
     * The default threshold after which revisions will be removed.
     * null => no threshold
     * set:
     * protected $revisionThreshold = 123;
     * on the model, which uses the HasRevisions trait to apply more
     * fine grained control.
     */
    'default_threshold' => null,

    /**
     * The default excluded fields, which will not be logged by revisions.
     * null => no excluded fields
     * set:
     * protected $revisionExclude = ['password', 'remember_token'];
     * on the model, which uses the HasRevisions trait to apply more
     * fine grained control.
     */
    'default_exclude' => ['created_at', 'updated_at'],

];
