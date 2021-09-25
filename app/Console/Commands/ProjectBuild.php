<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'build local development project';

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
     * @return int
     */
    public function handle()
    {
        $this->call('key:generate');
        $this->call('jwt:secret', ['--always-no' => 'default']);
        $this->call('migrate --seed');
    }
}
