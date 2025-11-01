<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;

class UpdateItemNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-item-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the name of all items based on their type and hierarchy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update item names...');

        $progressBar = $this->output->createProgressBar(Item::count());
        $progressBar->start();

        Item::chunkById(200, function ($items) use ($progressBar) {
            foreach ($items as $item) {
                $item->save();
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->info('\nAll item names have been updated successfully!');

        return 0;
    }
}