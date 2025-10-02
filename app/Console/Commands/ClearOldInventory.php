<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ClearOldInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:clear-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(90);

        $deleted = Inventory::where('last_updated', '<', $cutoffDate)->delete();

        if ($deleted) {
            Cache::forget(InventoryService::CACHE_KEY_INVENTORY);
        }

        $this->info("{$deleted} registros de estoque antigos foram removidos.");

        return CommandAlias::SUCCESS;
    }
}
