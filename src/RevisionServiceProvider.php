<?php

namespace LukasJankowski\Revision;

use Illuminate\Support\ServiceProvider;

class RevisionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/revisions.php' => config_path('/revisions.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_revisions_table.php' =>
                database_path("/migrations/" . date('Y_m_d_His', time()) . "_create_revisions_table.php"),
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/revisions.php',
            'revisions'
        );
    }
}
