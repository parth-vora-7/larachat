<?php

namespace xparthxvorax\larachat\Commands;

use Illuminate\Console\Command;

class LaraChatInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larachat:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To build database and publish assests for Larachat';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('migrate', [
            '--path' => realpath(__DIR__ . '../Migrations'),
        ]);
        $this->call('vendor:publish', [
            '--tag'   => 'larachat',
            '--force' => true,
        ]);
    }
}
