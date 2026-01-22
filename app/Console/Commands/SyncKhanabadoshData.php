<?php

namespace App\Console\Commands;

use App\Services\KhanabadoshSyncService;
use Illuminate\Console\Command;

class SyncKhanabadoshData extends Command
{
    protected $signature = 'khanabadosh:sync';
    protected $description = 'Sync products and collections from khanabadoshonline.com JSON feeds';

    public function handle(KhanabadoshSyncService $service): int
    {
        $this->info('Starting sync...');

        $summary = $service->sync();

        $this->table(
            ['Collections', 'Products', 'Variants', 'Images', 'Collection Links'],
            [[
                $summary['collections'] ?? 0,
                $summary['products'] ?? 0,
                $summary['variants'] ?? 0,
                $summary['images'] ?? 0,
                $summary['collection_links'] ?? 0,
            ]]
        );

        $this->info('Sync completed.');

        return self::SUCCESS;
    }
}
