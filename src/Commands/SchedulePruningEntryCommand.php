<?php

namespace Insense\LaravelTelescopePruning\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Insense\LaravelTelescopePruning\PruneEntries;

class TrimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telescope:trim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trim entries from the Telescope database';

    /**
     * Execute the console command.
     *
     * @param  Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function handle(Application $app)
    {
        $numTrimmed = (new PruneEntries($app))->prune();
        
        $this->info($numTrimmed . ' entries trimmed.');
    }
}

