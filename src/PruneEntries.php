<?php

namespace Insense\LaravelTelescopePruning;

use Insense\LaravelTelescopePruning\Models\EntryModel;

class PruneEntries
{

    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The pruning configuration options.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new prune entries instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;

        $config = $this->app['config'];
        $this->config = $config->get('telescope-pruning');
    }

    /**
     * Prune the Telescope entries.
     *
     * @return int
     */
    public function prune()
    {
        if (!$this->isEnabled() || is_null($this->config['limit'])) {
            return 0;
        }

        $limit = (int) $this->config['limit'];
        $whitelistedLimit = $this->config['whitelist']['whitelist_limit'];

        return EntryModel::prune($limit, $whitelistedLimit);
    }

    /**
     * Check if Telescope pruning is enabled.
     *
     * @return boolean
     */
    protected function isEnabled()
    {
        if (is_null($this->config['enabled'])) {
            return $this->app->environment('local');
        }

        return (bool) $this->config['enabled'];
    }
}
