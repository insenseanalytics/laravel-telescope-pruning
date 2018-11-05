<?php

namespace Insense\LaravelTelescopePruning\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Insense\LaravelTelescopePruning\PruneEntries;

class SchedulePruningEntryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telescope:prune-scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start pruning of entries from Telescope';

    /**
     * Execute the console command.
     *
     * @param  Illuminate\Foundation\Application  $app
     * @return void
     */
    public function handle(Application $app)
    {
        $this->info('Telescope entries pruning started!');
        
        (new PruneEntries($app))->prune();
        
        $this->info('Telescope entries pruning ende!');
    }
}

