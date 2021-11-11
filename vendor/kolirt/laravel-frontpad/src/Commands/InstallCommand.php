<?php

namespace Kolirt\Frontpad\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'frontpad:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instalation package';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('vendor:publish', ['--provider' => 'Kolirt\\Frontpad\\ServiceProvider']);
    }
}
