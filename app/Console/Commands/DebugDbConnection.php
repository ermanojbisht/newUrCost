<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DebugDbConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:db-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays the default database connection driver being used by the application.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultConnection = config('database.default');
        $driver = config("database.connections.{$defaultConnection}.driver");

        $this->info("Default Connection Name: " . $defaultConnection);
        $this->info("Database Driver in Use: " . $driver);
    }
}
